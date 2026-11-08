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
            $this->deck->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            AgentLog::create([
                'study_deck_id' => $this->deck->id,
                'kind' => 'Ralat',
                'message' => 'Kegagalan penjanaan modul AI: '.$e->getMessage(),
                'badge' => 'alert',
            ]);

            throw $e;
        }
    }
}
