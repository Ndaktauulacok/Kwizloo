<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AgentLog;
use App\Models\StudyDeck;
use Illuminate\Support\Facades\DB;

class StudyDeckGeneratorService
{
    private readonly GeminiService $gemini;

    public function __construct(
        private readonly ContentValidationService $validation = new ContentValidationService,
        ?GeminiService $gemini = null,
    ) {
        $this->gemini = $gemini ?? app(GeminiService::class);
    }

    /**
     * Create a study deck from the verified sample pack.
     */
    public function createFromSample(): StudyDeck
    {
        $sample = SampleDeckProvider::getPhotosynthesisSample();

        return DB::transaction(function () use ($sample): StudyDeck {
            $deck = StudyDeck::create([
                'title' => $sample['title'],
                'source_text' => $sample['note'],
                'total_words' => count(preg_split('/\s+/u', trim($sample['note'])) ?: []),
                'mode' => 'sim',
                'status' => 'completed',
                'progress' => 100,
            ]);

            foreach ($sample['topics'] as $tData) {
                $topic = $deck->topics()->create([
                    'name' => $tData['nama'],
                    'summary' => $tData['ringkasan'],
                    'status' => 'active',
                    'hp' => 3,
                    'max_hp' => 3,
                    'strategy' => [
                        'level' => 2,
                        'hint' => false,
                        'note' => 'Permulaan modul latihan topik.',
                    ],
                ]);

                foreach ($tData['soalan'] as $q) {
                    $topic->questions()->create([
                        'study_deck_id' => $deck->id,
                        'question' => $q['soalan'],
                        'options' => $q['pilihan'],
                        'correct_index' => $q['jawapan'],
                        'difficulty' => $q['tahap'],
                        'explanation' => $q['penjelasan'],
                        'quote' => $q['petikan'],
                    ]);
                }

                foreach ($tData['kad'] as $k) {
                    $topic->flashcards()->create([
                        'study_deck_id' => $deck->id,
                        'front' => $k['depan'],
                        'back' => $k['belakang'],
                        'quote' => $k['petikan'],
                    ]);
                }
            }

            AgentLog::create([
                'study_deck_id' => $deck->id,
                'kind' => 'Keputusan',
                'message' => 'Pek modul berjaya dicipta: 3 topik, 15 soalan, dan 12 flashcard siap disahkan.',
                'badge' => 'semak',
            ]);

            return $deck;
        });
    }

    /**
     * Create a pending study deck placeholder before async generation starts.
     */
    public function createPending(string $title, string $sourceText): StudyDeck
    {
        $cleanText = $this->validation->sanitizeText($sourceText);
        $words = preg_split('/\s+/u', trim($cleanText)) ?: [];
        $totalWords = count($words);

        return StudyDeck::create([
            'title' => $title !== '' ? $title : 'Modul Pembelajaran Tersuai',
            'source_text' => $cleanText,
            'total_words' => $totalWords,
            'mode' => 'ai',
            'status' => 'pending',
            'progress' => 10,
        ]);
    }

    /**
     * Synchronous creation wrapper for tests or direct execution.
     */
    public function createFromText(string $title, string $sourceText, ?string $aiDriver = null): StudyDeck
    {
        $deck = $this->createPending($title, $sourceText);
        $this->generateDeckContent($deck);
        $deck->update([
            'status' => 'completed',
            'progress' => 100,
        ]);

        return $deck;
    }

    /**
     * Generate structured topics, questions, and flashcards using Gemini or heuristic fallback.
     */
    public function generateDeckContent(StudyDeck $deck): void
    {
        $deck->update(['progress' => 35]);

        $schema = [
            'type' => 'OBJECT',
            'properties' => [
                'topics' => [
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'name' => ['type' => 'STRING'],
                            'summary' => ['type' => 'STRING'],
                            'questions' => [
                                'type' => 'ARRAY',
                                'items' => [
                                    'type' => 'OBJECT',
                                    'properties' => [
                                        'question' => ['type' => 'STRING'],
                                        'options' => [
                                            'type' => 'ARRAY',
                                            'items' => ['type' => 'STRING'],
                                        ],
                                        'correct_index' => ['type' => 'INTEGER'],
                                        'difficulty' => ['type' => 'INTEGER'],
                                        'explanation' => ['type' => 'STRING'],
                                        'quote' => ['type' => 'STRING'],
                                    ],
                                    'required' => ['question', 'options', 'correct_index', 'difficulty', 'explanation', 'quote'],
                                ],
                            ],
                            'flashcards' => [
                                'type' => 'ARRAY',
                                'items' => [
                                    'type' => 'OBJECT',
                                    'properties' => [
                                        'front' => ['type' => 'STRING'],
                                        'back' => ['type' => 'STRING'],
                                        'quote' => ['type' => 'STRING'],
                                    ],
                                    'required' => ['front', 'back', 'quote'],
                                ],
                            ],
                        ],
                        'required' => ['name', 'summary', 'questions', 'flashcards'],
                    ],
                ],
            ],
            'required' => ['topics'],
        ];

        $prompt = "Berdasarkan nota teks berikut, sila bina topik-topik pembelajaran (antara 2 hingga 4 topik). Bagi setiap topik, hasilkan 3 hingga 5 soalan kuiz aneka pilihan (4 pilihan) dan 3 hingga 5 kad imbasan (front/back). Setiap soalan dan kad imbasan WAJIB disertakan 'quote' (petikan teks langsung) daripada teks nota. Teks nota:\n\n{$deck->source_text}";

        $system = 'Anda ialah pakar reka bentuk kurikulum pintar. Anda hanya menggunakan fakta tepat yang terdapat dalam teks nota untuk membina soalan dan flashcard tanpa halusinasi.';

        $result = $this->gemini->generateStructured($prompt, $schema, $system);
        $topicsData = $result['topics'] ?? [];

        $deck->update(['progress' => 70]);

        DB::transaction(function () use ($deck, $topicsData): void {
            if (empty($topicsData)) {
                $this->populateHeuristicFallback($deck);

                return;
            }

            foreach ($topicsData as $tData) {
                $topic = $deck->topics()->create([
                    'name' => (string) ($tData['name'] ?? 'Topik Pembelajaran'),
                    'summary' => (string) ($tData['summary'] ?? 'Ringkasan topik.'),
                    'status' => 'active',
                    'hp' => 3,
                    'max_hp' => 3,
                    'strategy' => [
                        'level' => 2,
                        'hint' => false,
                        'note' => 'Modul dijana oleh AI Gemini berasaskan nota.',
                    ],
                ]);

                foreach ($tData['questions'] ?? [] as $q) {
                    $options = is_array($q['options'] ?? null) ? array_values($q['options']) : [];
                    if (count($options) < 4) {
                        $options = array_pad($options, 4, 'Pilihan tambahan');
                    }

                    $topic->questions()->create([
                        'study_deck_id' => $deck->id,
                        'question' => (string) ($q['question'] ?? 'Soalan konsep'),
                        'options' => $options,
                        'correct_index' => (int) ($q['correct_index'] ?? 0),
                        'difficulty' => max(1, min(3, (int) ($q['difficulty'] ?? 1))),
                        'explanation' => (string) ($q['explanation'] ?? 'Disahkan berasaskan teks.'),
                        'quote' => (string) ($q['quote'] ?? mb_substr($deck->source_text, 0, 50)),
                    ]);
                }

                foreach ($tData['flashcards'] ?? [] as $k) {
                    $topic->flashcards()->create([
                        'study_deck_id' => $deck->id,
                        'front' => (string) ($k['front'] ?? 'Istilah'),
                        'back' => (string) ($k['back'] ?? 'Definisi'),
                        'quote' => (string) ($k['quote'] ?? mb_substr($deck->source_text, 0, 50)),
                    ]);
                }
            }

            $topicCount = $deck->topics()->count();
            $qCount = $deck->questions()->count();
            $fCount = $deck->flashcards()->count();

            AgentLog::create([
                'study_deck_id' => $deck->id,
                'kind' => 'Keputusan',
                'message' => "Pek modul AI berjaya dibina: {$topicCount} topik, {$qCount} soalan, dan {$fCount} flashcard.",
                'badge' => 'semak',
            ]);
        });

        $deck->update(['progress' => 95]);
    }

    /**
     * Fallback heuristic logic if Gemini is unavailable or returns no topics.
     */
    private function populateHeuristicFallback(StudyDeck $deck): void
    {
        $paragraphs = array_values(array_filter(
            array_map('trim', explode("\n\n", str_replace("\r", '', $deck->source_text))),
            fn ($p) => mb_strlen($p) > 20
        ));

        if (empty($paragraphs)) {
            $paragraphs = [mb_substr($deck->source_text, 0, 1000)];
        }

        $topicIndex = 1;
        foreach ($paragraphs as $para) {
            $firstSentence = trim(explode('.', $para)[0] ?? 'Topik '.$topicIndex);
            $topicName = mb_substr($firstSentence, 0, 50);

            $topic = $deck->topics()->create([
                'name' => $topicName !== '' ? $topicName : ('Konsep '.$topicIndex),
                'summary' => mb_substr($para, 0, 120),
                'status' => 'active',
                'hp' => 3,
                'max_hp' => 3,
                'strategy' => [
                    'level' => 2,
                    'hint' => false,
                    'note' => 'Modul diperolehi daripada nota teks.',
                ],
            ]);

            $sentences = array_values(array_filter(
                array_map('trim', explode('.', $para)),
                fn ($s) => mb_strlen($s) > 15
            ));

            foreach ($sentences as $idx => $sentence) {
                $sampleQuote = mb_substr($sentence, 0, 40);
                $topic->flashcards()->create([
                    'study_deck_id' => $deck->id,
                    'front' => 'Konsep penting: '.mb_substr($sentence, 0, 30).'...',
                    'back' => $sentence.'.',
                    'quote' => $sampleQuote,
                ]);

                $topic->questions()->create([
                    'study_deck_id' => $deck->id,
                    'question' => 'Berdasarkan petikan, apakah maksud: "'.mb_substr($sentence, 0, 45).'..."?',
                    'options' => [
                        $sentence.'.',
                        'Pernyataan ini bertentangan dengan kandungan teks.',
                        'Kandungan ini merujuk kepada konsep luar sukatan.',
                        'Tiada kaitan langsung dengan bahagian ini.',
                    ],
                    'correct_index' => 0,
                    'difficulty' => ($idx % 3) + 1,
                    'explanation' => 'Jawapan disahkan berdasarkan teks rujukan.',
                    'quote' => $sampleQuote,
                ]);

                if ($idx >= 3) {
                    break;
                }
            }

            $topicIndex++;
            if ($topicIndex > 3) {
                break;
            }
        }
    }
}
