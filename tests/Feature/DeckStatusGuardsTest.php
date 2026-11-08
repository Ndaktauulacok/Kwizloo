<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\GenerateStudyDeckJob;
use App\Models\StudyDeck;
use App\Services\GeminiService;
use App\Services\StudyDeckGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeckStatusGuardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_study_hub_redirects_to_processing_while_deck_is_not_ready(): void
    {
        $deck = StudyDeck::create([
            'title' => 'Nota Belum Siap',
            'source_text' => 'Teks nota yang masih diproses oleh ejen AI.',
            'status' => 'processing',
            'progress' => 40,
        ]);

        $this->get(route('decks.show', $deck))
            ->assertRedirect(route('decks.processing', $deck));
    }

    public function test_fallback_retry_does_not_rebuild_a_completed_deck(): void
    {
        $deck = app(StudyDeckGeneratorService::class)->createFromSample();
        $topicIds = $deck->topics()->pluck('id')->all();

        $this->post(route('decks.retry-fallback', $deck))
            ->assertRedirect(route('decks.show', $deck));

        $this->assertSame($topicIds, $deck->topics()->pluck('id')->all());
    }

    public function test_generation_job_skips_a_deck_already_completed_by_fallback(): void
    {
        $deck = StudyDeck::create([
            'title' => 'Siap Dalam Mod Asas',
            'source_text' => 'Klorofil menyerap cahaya matahari untuk fotosintesis.',
            'status' => 'completed',
            'progress' => 100,
        ]);

        $this->mock(GeminiService::class, function ($mock): void {
            $mock->shouldNotReceive('generateStructured');
        });

        (new GenerateStudyDeckJob($deck))->handle(app(StudyDeckGeneratorService::class));

        $this->assertSame('completed', $deck->fresh()->status);
    }
}
