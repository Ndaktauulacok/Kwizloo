<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AgentLog;
use App\Models\StudyDeck;
use App\Models\TeacherAiReport;
use App\Services\GeminiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class GenerateTeacherReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;

    public int $tries = 1;

    public function __construct(
        public StudyDeck $deck,
    ) {}

    public function handle(GeminiService $gemini): void
    {
        $this->deck->load(['topics', 'studySessions']);

        /** @var TeacherAiReport $report */
        $report = $this->deck->teacherAiReport ?? TeacherAiReport::create([
            'study_deck_id' => $this->deck->id,
            'status' => 'processing',
        ]);

        $report->update(['status' => 'processing']);

        try {
            $totalRight = $this->deck->studySessions->sum('right_count');
            $totalWrong = $this->deck->studySessions->sum('wrong_count');
            $totalSessions = $this->deck->studySessions->count();
            $topicSummaries = $this->deck->topics->map(function ($t) {
                return "- {$t->name}: Betul={$t->right_count}, Salah={$t->wrong_count}, Ketepatan={$t->accuracy_percentage}%";
            })->implode("\n");

            $mistakes = $this->deck->topics->flatMap(fn ($t) => $t->mistakes ?? [])->take(8)->implode("\n- ");

            $schema = [
                'type' => 'OBJECT',
                'properties' => [
                    'summary' => ['type' => 'STRING'],
                    'misconceptions' => [
                        'type' => 'ARRAY',
                        'items' => ['type' => 'STRING'],
                    ],
                    'recommendations' => [
                        'type' => 'ARRAY',
                        'items' => ['type' => 'STRING'],
                    ],
                ],
                'required' => ['summary', 'misconceptions', 'recommendations'],
            ];

            $prompt = "Sebagai pakar pedagogi dan penasihat diagnostik guru, analisis prestasi pelajar bagi modul '{$this->deck->title}'.\n"
                ."Jumlah sesi: {$totalSessions}, Jumlah jawapan betul: {$totalRight}, Jumlah salah: {$totalWrong}.\n"
                ."Prestasi mengikut topik:\n{$topicSummaries}\n"
                .($mistakes !== '' ? "Contoh kesilapan pelajar:\n- {$mistakes}\n" : '')
                ."Sila sediakan:\n"
                ."1. 'summary': Ringkasan eksekutif 2-3 ayat tentang tahap kefahaman murid dan jurang pembelajaran utama dalam Bahasa Melayu.\n"
                ."2. 'misconceptions': Senarai 2-4 salah faham konsep utama yang dikesan.\n"
                ."3. 'recommendations': Senarai 2-4 cadangan tindakan intervensi bilik darjah yang praktikal.";

            $system = 'Anda ialah penasihat akademik diagnostik sekolah yang pakar menganalisis corak kesilapan pelajar dan memberikan cadangan pemulihan kurikulum.';

            $data = $gemini->generateStructured($prompt, $schema, $system, throwOnError: $gemini->isConfigured());

            $report->update([
                'status' => 'completed',
                'summary' => (string) ($data['summary'] ?? 'Analisis prestasi bilik darjah selesai.'),
                'misconceptions' => is_array($data['misconceptions'] ?? null) ? $data['misconceptions'] : [],
                'recommendations' => is_array($data['recommendations'] ?? null) ? $data['recommendations'] : [],
                'error_message' => null,
            ]);

            AgentLog::create([
                'study_deck_id' => $this->deck->id,
                'kind' => 'Keputusan',
                'message' => 'Laporan diagnostik AI untuk guru berjaya disediakan.',
                'badge' => 'semak',
            ]);
        } catch (Throwable $e) {
            $report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            AgentLog::create([
                'study_deck_id' => $this->deck->id,
                'kind' => 'Ralat',
                'message' => 'Kegagalan menjana laporan AI guru: '.$e->getMessage(),
                'badge' => 'alert',
            ]);

            throw $e;
        }
    }
}
