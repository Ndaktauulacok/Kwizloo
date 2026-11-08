<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AgentLog;
use App\Models\StudyDeck;
use App\Services\StudyDeckGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class GenerateStudyDeckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public int $tries = 1;

    public function __construct(
        public StudyDeck $deck,
    ) {}

    public function handle(StudyDeckGeneratorService $generator): void
    {
        $this->deck->refresh();

        if ($this->deck->status === 'completed') {
            return;
        }

        try {
            $this->deck->update([
                'status' => 'processing',
                'progress' => 20,
            ]);

            $generator->generateDeckContent($this->deck);

            $this->deck->update([
                'status' => 'completed',
                'progress' => 100,
            ]);
        } catch (Throwable $e) {
            $friendlyMessage = $this->sanitizeErrorMessage($e->getMessage());

            $this->deck->update([
                'status' => 'failed',
                'error_message' => $friendlyMessage,
            ]);

            AgentLog::create([
                'study_deck_id' => $this->deck->id,
                'kind' => 'Ralat',
                'message' => 'Kegagalan penjanaan modul AI: '.$friendlyMessage,
                'badge' => 'alert',
            ]);

            throw $e;
        }
    }

    private function sanitizeErrorMessage(string $message): string
    {
        $lower = mb_strtolower($message);

        if (str_contains($lower, '429') || str_contains($lower, 'quota') || str_contains($lower, 'resource_exhausted')) {
            return 'Had kuota harian Gemini AI (Free Tier) telah dicapai (20 permintaan sehari). Sila cuba lagi kemudian atau bina modul dalam mod asas.';
        }

        if (str_contains($lower, '401') || str_contains($lower, '403') || str_contains($lower, 'api key')) {
            return 'Kunci Gemini API tidak sah atau tiada kebenaran akses.';
        }

        if (str_contains($lower, 'timeout') || str_contains($lower, 'timed out')) {
            return 'Sambungan ke pelayan Gemini AI tamat tempoh. Sila cuba lagi.';
        }

        if (str_contains($message, '{"error"') || str_contains($message, '{ "error"')) {
            return 'Ralat perkhidmatan AI luar. Sila gunakan mod asas untuk membina modul serta-merta.';
        }

        return $message;
    }
}
