<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\GenerateStudyDeckJob;
use App\Models\StudyDeck;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AsyncDeckGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploading_text_dispatches_generate_study_deck_job_and_redirects_to_processing(): void
    {
        Queue::fake();

        $response = $this->post(route('decks.store'), [
            'title' => 'Nota Sains Tingkatan 4',
            'note_text' => str_repeat('Fotosintesis merupakan proses biokimia penting tumbuhan hijau. ', 5),
        ]);

        $deck = StudyDeck::first();
        $this->assertNotNull($deck);
        $this->assertEquals('pending', $deck->status);

        Queue::assertPushed(GenerateStudyDeckJob::class, function ($job) use ($deck) {
            return $job->deck->id === $deck->id;
        });

        $response->assertRedirect(route('decks.processing', $deck));
    }

    public function test_deck_status_endpoint_returns_json_progress(): void
    {
        $deck = StudyDeck::create([
            'title' => 'Sample Deck',
            'source_text' => 'Content here',
            'status' => 'processing',
            'progress' => 50,
        ]);

        $response = $this->getJson(route('decks.status', $deck));

        $response->assertOk()
            ->assertJson([
                'status' => 'processing',
                'progress' => 50,
            ]);
    }

    public function test_generate_study_deck_job_processes_content_and_marks_completed(): void
    {
        $deck = StudyDeck::create([
            'title' => 'Nota Biologi',
            'source_text' => 'Klorofil menyerap cahaya matahari untuk fotosintesis. Karbon dioksida dan air ditukarkan kepada glukosa.',
            'status' => 'pending',
            'progress' => 5,
        ]);

        $this->mock(GeminiService::class, function ($mock) {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('generateStructured')->andReturn([
                'topics' => [
                    [
                        'name' => 'Fasa Fotosintesis',
                        'summary' => 'Tindak balas cahaya dan gelap.',
                        'questions' => [
                            [
                                'question' => 'Apakah fungsi klorofil?',
                                'options' => [
                                    'Menyerap tenaga cahaya',
                                    'Menghasilkan oksigen secara pasif',
                                    'Menyerap air dari tanah',
                                    'Membakar glukosa',
                                ],
                                'correct_index' => 0,
                                'difficulty' => 1,
                                'explanation' => 'Klorofil bertindak sebagai pigmen penyerap cahaya.',
                                'quote' => 'Klorofil menyerap cahaya matahari.',
                            ],
                        ],
                        'flashcards' => [
                            [
                                'front' => 'Fungsi utama klorofil',
                                'back' => 'Menyerap cahaya matahari untuk fotosintesis',
                                'quote' => 'Klorofil menyerap cahaya matahari.',
                            ],
                        ],
                    ],
                ],
            ]);
        });

        $job = new GenerateStudyDeckJob($deck);
        $job->handle(app(\App\Services\StudyDeckGeneratorService::class));

        $fresh = $deck->fresh();
        $this->assertEquals('completed', $fresh->status);
        $this->assertEquals(100, $fresh->progress);
        $this->assertCount(1, $fresh->topics);
        $this->assertCount(1, $fresh->questions);
        $this->assertCount(1, $fresh->flashcards);
    }
}
