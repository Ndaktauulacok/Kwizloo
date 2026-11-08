<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AgentLog;
use App\Models\StudyDeck;
use App\Models\Topic;
use Illuminate\Support\Facades\Cache;
use Throwable;

class AdaptiveAgentService
{
    private readonly GeminiService $gemini;

    public function __construct(?GeminiService $gemini = null)
    {
        $this->gemini = $gemini ?? app(GeminiService::class);
    }

    /**
     * Study activity definitions with pedagogical intent.
     */
    public const ACTIVITIES = [
        'boss' => [
            'name' => 'Penilaian Penguasaan (Mastery Assessment)',
            'description' => 'Tewaskan jurang pemahaman dengan menjawab soalan aras tinggi.',
        ],
        'blitz' => [
            'name' => 'Ujian Pantas (Speed Recall)',
            'description' => '60 saat untuk menguji kelancaran pengingatan konsep.',
        ],
        'rain' => [
            'name' => 'Sintesis Menurun (Descending Synthesis)',
            'description' => 'Tangkap jawapan tepat sebelum masa berlalu.',
        ],
        'match' => [
            'name' => 'Padanan Konsep (Term Association)',
            'description' => 'Padankan istilah penting dengan definisinya.',
        ],
        'tower' => [
            'name' => 'Menara Kefahaman (Ascending Comprehension)',
            'description' => 'Bina tangga ilmu langkah demi langkah mengikut tahap.',
        ],
        'tf' => [
            'name' => 'Pengesahan Kritis (Critical Verification)',
            'description' => 'Analisis kebenaran pernyataan fakta dan konsep.',
        ],
        'word' => [
            'name' => 'Rekonstruksi Istilah (Term Reconstruction)',
            'description' => 'Eja dan kenal pasti istilah saintifik utama.',
        ],
        'cards' => [
            'name' => 'Flashcard Berjarak (Spaced Recall)',
            'description' => 'Ulang kaji aktif konsep asas dengan kad imbasan.',
        ],
        'race' => [
            'name' => 'Penanda Aras Adaptif (Pacing Benchmark)',
            'description' => 'Berlumba dengan bot pembelajaran beraras automatik.',
        ],
    ];

    /**
     * Determine the best next mission based on cognitive load & mastery distribution.
     *
     * @return array{
     *     topic_id: int,
     *     topic_name: string,
     *     activity: string,
     *     activity_name: string,
     *     rationale: string
     * }
     */
    public function determineNextMission(StudyDeck $deck): array
    {
        $topics = $deck->topics()->get();
        if ($topics->isEmpty()) {
            return [
                'topic_id' => 0,
                'topic_name' => 'Umum',
                'activity' => 'blitz',
                'activity_name' => self::ACTIVITIES['blitz']['name'],
                'rationale' => 'Mula dengan ujian pantas untuk menetapkan penanda aras.',
            ];
        }

        // Check for active boss topics (knowledge gap materialized)
        $bosses = $topics->filter(fn (Topic $t) => $t->status === 'boss')->sortByDesc('hp');

        if ($bosses->isNotEmpty()) {
            $b = $bosses->first();
            $acc = $b->accuracy_percentage;

            if ($b->losses > 0) {
                $mission = [
                    'topic_id' => $b->id,
                    'topic_name' => $b->name,
                    'activity' => 'cards',
                    'activity_name' => self::ACTIVITIES['cards']['name'],
                    'rationale' => "Kamu menghadapi kesukaran dalam {$b->name}. Ulang kaji konsep asas melalui flashcard dahulu sebelum mencuba semula.",
                ];
            } elseif ($acc < 40) {
                $mission = [
                    'topic_id' => $b->id,
                    'topic_name' => $b->name,
                    'activity' => 'cards',
                    'activity_name' => self::ACTIVITIES['cards']['name'],
                    'rationale' => "Ketepatan bagi {$b->name} masih rendah ({$acc}%). Kukuhkan memori dengan flashcard aktif.",
                ];
            } elseif ($acc < 65) {
                $mission = [
                    'topic_id' => $b->id,
                    'topic_name' => $b->name,
                    'activity' => 'match',
                    'activity_name' => self::ACTIVITIES['match']['name'],
                    'rationale' => "Kamu hampir menguasai {$b->name}. Padanan konsep akan memperkukuh perkaitan fakta.",
                ];
            } elseif ($b->attempts === 0 && $acc < 85) {
                $mission = [
                    'topic_id' => $b->id,
                    'topic_name' => $b->name,
                    'activity' => 'tower',
                    'activity_name' => self::ACTIVITIES['tower']['name'],
                    'rationale' => "Bina kefahaman berperingkat dalam {$b->name} sebelum menghadapi penilaian penguasaan.",
                ];
            } else {
                $mission = [
                    'topic_id' => $b->id,
                    'topic_name' => $b->name,
                    'activity' => 'boss',
                    'activity_name' => self::ACTIVITIES['boss']['name'],
                    'rationale' => "Kefahaman kamu kukuh. Bersedia untuk menewaskan jurang penguasaan dalam {$b->name}!",
                ];
            }

            return $this->enrichMissionRationale($deck, $mission);
        }

        // If no active boss, pick the topic with lowest accuracy
        $lowest = $topics->sortBy(fn (Topic $t) => $t->accuracy_percentage)->first();

        $mission = [
            'topic_id' => $lowest->id,
            'topic_name' => $lowest->name,
            'activity' => 'blitz',
            'activity_name' => self::ACTIVITIES['blitz']['name'],
            'rationale' => 'Semua jurang utama telah diatasi. Teruskan latihan kelajuan ingatan untuk pengekalan jangka panjang.',
        ];

        return $this->enrichMissionRationale($deck, $mission);
    }

    /**
     * @param  array{topic_id: int, topic_name: string, activity: string, activity_name: string, rationale: string}  $mission
     * @return array{topic_id: int, topic_name: string, activity: string, activity_name: string, rationale: string}
     */
    private function enrichMissionRationale(StudyDeck $deck, array $mission): array
    {
        if (! $this->gemini->isConfigured()) {
            return $mission;
        }

        try {
            $cacheKey = "deck_{$deck->id}_adaptive_rationale_{$mission['topic_id']}_{$mission['activity']}";

            $aiRationale = Cache::remember($cacheKey, 300, function () use ($deck, $mission): string {
                $prompt = "Sebagai tutor AI pendidikan pintar, berikan satu ayat nasihat pedagogi ringkas bermula dengan 'Analisis AI:' dalam Bahasa Melayu. Modul: '{$deck->title}', Topik tumpuan: '{$mission['topic_name']}', Aktiviti: '{$mission['activity_name']}'. Nasihat:";

                $text = $this->gemini->generateText($prompt, null, false, 2);

                return trim($text);
            });

            if ($aiRationale !== '' && str_contains($aiRationale, 'Analisis AI:')) {
                $mission['rationale'] = $aiRationale;
            }
        } catch (Throwable) {
            // Keep deterministic fallback rationale
        }

        return $mission;
    }

    /**
     * Process diagnostic quick scan results and spawn boss states for weak concepts.
     */
    public function evaluateScanResults(StudyDeck $deck, array $wrongQuestionIds): void
    {
        $topics = $deck->topics()->with('questions')->get();

        foreach ($topics as $topic) {
            $topicWrongCount = $topic->questions->whereIn('id', $wrongQuestionIds)->count();

            if ($topicWrongCount > 0) {
                $hp = min(6, 3 + $topicWrongCount);
                $topic->update([
                    'status' => 'boss',
                    'hp' => $hp,
                    'max_hp' => $hp,
                    'strategy' => [
                        'level' => 2,
                        'hint' => false,
                        'note' => "Topik ini dikesan memerlukan latihan intensif ({$topicWrongCount} kesilapan diagnostik).",
                    ],
                ]);

                AgentLog::create([
                    'study_deck_id' => $deck->id,
                    'topic_id' => $topic->id,
                    'kind' => 'Keputusan',
                    'message' => "Jurang dikesan pada \"{$topic->name}\". Penilaian penguasaan ditetapkan pada {$hp} HP.",
                    'badge' => 'boss',
                ]);
            } else {
                $topic->update(['status' => 'active']);
            }
        }
    }

    /**
     * Record a learner attempt on a topic.
     */
    public function recordAttempt(Topic $topic, bool $isCorrect, ?string $mistakeQuestion = null): void
    {
        $topic->attempts++;

        if ($isCorrect) {
            $topic->right_count++;
            if ($topic->status === 'boss' && $topic->hp > 0) {
                $topic->hp--;
                if ($topic->hp === 0) {
                    $topic->status = 'tewas'; // Mastered / defeated
                    AgentLog::create([
                        'study_deck_id' => $topic->study_deck_id,
                        'topic_id' => $topic->id,
                        'kind' => 'Keputusan',
                        'message' => "Topik \"{$topic->name}\" berjaya dikuasai sepenuhnya oleh pelajar!",
                        'badge' => 'semak',
                    ]);
                }
            }
        } else {
            $topic->wrong_count++;
            if ($mistakeQuestion !== null) {
                $mistakes = $topic->mistakes ?? [];
                $mistakes[] = $mistakeQuestion;
                $topic->mistakes = array_slice($mistakes, -10); // keep last 10
            }

            // If not boss yet, escalate to boss if multiple errors occur
            if ($topic->status !== 'boss' && $topic->wrong_count >= 2) {
                $topic->status = 'boss';
                $topic->hp = 3;
                $topic->max_hp = 3;
            } elseif ($topic->status === 'boss' && $topic->hp < 6) {
                $topic->hp++;
                $topic->max_hp = max($topic->max_hp, $topic->hp);
            }
        }

        $topic->save();
    }

    /**
     * Adjust difficulty and activate pedagogical scaffolding after a boss loss.
     *
     * @return array{level: int, hint: bool, note: string}
     */
    public function adjustStrategyAfterLoss(Topic $topic): array
    {
        $topic->losses++;
        $currentStrategy = $topic->strategy ?? ['level' => 2, 'hint' => false, 'note' => ''];
        $newLevel = max(1, ((int) ($currentStrategy['level'] ?? 2)) - 1);

        $note = $topic->losses >= 2
            ? 'Pelajar menghadapi kesukaran berulang. Topik ini ditandakan dalam laporan untuk perhatian guru.'
            : 'Aras soalan dilaraskan lebih rendah dan petunjuk teks diaktifkan.';

        $newStrategy = [
            'level' => $newLevel,
            'hint' => true,
            'note' => $note,
        ];

        $topic->strategy = $newStrategy;
        $topic->rematch_at = now()->addSeconds(30);
        $topic->save();

        AgentLog::create([
            'study_deck_id' => $topic->study_deck_id,
            'topic_id' => $topic->id,
            'kind' => 'Keputusan',
            'message' => "Pelan pedagogi dilaraskan bagi \"{$topic->name}\": Aras {$newLevel}, petunjuk aktif. {$note}",
            'badge' => $topic->losses >= 2 ? 'alert' : 'semak',
        ]);

        return $newStrategy;
    }
}
