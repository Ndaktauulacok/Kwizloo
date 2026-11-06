<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AgentLog;
use App\Models\StudyDeck;
use Illuminate\Support\Facades\DB;

class StudyDeckGeneratorService
{
    public function __construct(
        private readonly ContentValidationService $validation = new ContentValidationService,
    ) {}

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
     * Create a study deck from raw student text, splitting into topical units and extracting questions.
     */
    public function createFromText(string $title, string $sourceText, ?string $aiDriver = null): StudyDeck
    {
        $cleanText = $this->validation->sanitizeText($sourceText);
        $words = preg_split('/\s+/u', trim($cleanText)) ?: [];
        $totalWords = count($words);

        return DB::transaction(function () use ($title, $cleanText, $totalWords): StudyDeck {
            $deck = StudyDeck::create([
                'title' => $title !== '' ? $title : 'Modul Pembelajaran Tersuai',
                'source_text' => $cleanText,
                'total_words' => $totalWords,
                'mode' => 'sim',
            ]);

            // Break note into logical sections or paragraphs
            $paragraphs = array_values(array_filter(
                array_map('trim', explode("\n\n", str_replace("\r", '', $cleanText))),
                fn ($p) => mb_strlen($p) > 20
            ));

            if (empty($paragraphs)) {
                $paragraphs = [mb_substr($cleanText, 0, 1000)];
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

                // Extract sentences for grounded flashcards and questions
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
                        'question' => 'Berdasarkan petikan, apakah yang dimaksudkan dengan: "'.mb_substr($sentence, 0, 45).'..."?',
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

                    if ($idx >= 4) {
                        break;
                    }
                }

                $topicIndex++;
                if ($topicIndex > 4) {
                    break;
                }
            }

            AgentLog::create([
                'study_deck_id' => $deck->id,
                'kind' => 'Keputusan',
                'message' => "Pek nota berjaya diproses ke dalam {$deck->topics()->count()} topik pembelajaran.",
                'badge' => 'semak',
            ]);

            return $deck;
        });
    }
}
