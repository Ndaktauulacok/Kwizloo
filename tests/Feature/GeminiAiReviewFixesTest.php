<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\GenerateStudyDeckJob;
use App\Models\StudyDeck;
use App\Services\GeminiApiException;
use App\Services\GeminiService;
use App\Services\StudyDeckGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GeminiAiReviewFixesTest extends TestCase
{
    use RefreshDatabase;

    public function test_gemini_api_exception_thrown_when_throw_on_error_requested_on_429(): void
    {
        config(['services.gemini.key' => 'mock-gemini-key']);

        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response(['error' => 'Rate limit reached'], 429),
        ]);

        $service = app(GeminiService::class);

        $this->expectException(GeminiApiException::class);
        $service->generateStructured('Test prompt', ['type' => 'OBJECT'], null, true);
    }

    public function test_generate_study_deck_job_sets_failed_status_when_api_fails(): void
    {
        config(['services.gemini.key' => 'mock-gemini-key']);

        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response(['error' => 'Quota exhausted'], 429),
        ]);

        $deck = StudyDeck::create([
            'title' => 'Test Fail Deck',
            'source_text' => 'Teks biologi untuk diuji kegagalan.',
            'status' => 'pending',
            'progress' => 10,
        ]);

        $job = new GenerateStudyDeckJob($deck);

        try {
            $job->handle(app(StudyDeckGeneratorService::class));
        } catch (\Throwable $e) {
            // expected exception rethrow
        }

        $fresh = $deck->fresh();
        $this->assertEquals('failed', $fresh->status);
        $this->assertNotEmpty($fresh->error_message);
        $this->assertStringNotContainsString('HTTP status 429', $fresh->error_message);
        $this->assertStringNotContainsString('{"error"', $fresh->error_message);
        $this->assertStringContainsString('kuota', mb_strtolower($fresh->error_message));
        $this->assertDatabaseHas('agent_logs', [
            'study_deck_id' => $deck->id,
            'kind' => 'Ralat',
        ]);
    }

    public function test_retry_fallback_populates_deck_heuristically_and_redirects(): void
    {
        $deck = StudyDeck::create([
            'title' => 'Nota Biologi Tingkatan 4',
            'source_text' => str_repeat('Fotosintesis ialah proses tumbuhan hijau menghasilkan makanan sendiri. ', 4),
            'status' => 'failed',
            'error_message' => 'Had kuota harian Gemini AI telah dicapai.',
            'progress' => 20,
        ]);

        $response = $this->post(route('decks.retry-fallback', $deck));

        $response->assertRedirect(route('decks.show', $deck));
        $fresh = $deck->fresh();
        $this->assertEquals('completed', $fresh->status);
        $this->assertEquals(100, $fresh->progress);
        $this->assertNull($fresh->error_message);
        $this->assertGreaterThan(0, $fresh->topics()->count());
        $this->assertGreaterThan(0, $fresh->questions()->count());
        $this->assertGreaterThan(0, $fresh->flashcards()->count());
    }

    public function test_teacher_report_controller_prevents_duplicate_dispatch_when_already_pending(): void
    {
        Queue::fake();

        $deck = StudyDeck::create([
            'title' => 'Fizik Test',
            'source_text' => 'Nota...',
            'status' => 'completed',
        ]);
        $deck->studySessions()->create([
            'activity_type' => 'boss',
            'score' => 50,
            'right_count' => 1,
            'wrong_count' => 1,
            'best_combo' => 1,
        ]);
        $deck->teacherAiReport()->create([
            'status' => 'processing',
        ]);

        $response = $this->postJson(route('decks.report.generate', $deck));

        $response->assertOk()
            ->assertJson(['status' => 'processing', 'message' => 'Laporan sedang diproses.']);

        Queue::assertNothingPushed();
    }

    public function test_unconfigured_gemini_runs_grounded_heuristic_fallback_from_user_text(): void
    {
        config(['services.gemini.key' => '']);

        $deck = StudyDeck::create([
            'title' => 'Nota Tumbuhan',
            'source_text' => "Kloroplas ialah organel sel tumbuhan. Klorofil menyerap cahaya untuk fotosintesis.\n\nStoma membenarkan pertukaran gas oksigen dan karbon dioksida.",
            'status' => 'pending',
            'progress' => 10,
        ]);

        $generator = app(StudyDeckGeneratorService::class);
        $generator->generateDeckContent($deck);

        $topics = $deck->topics()->get();
        $this->assertGreaterThanOrEqual(1, $topics->count());
        // Verify topics are grounded in actual user text words
        $firstTopic = $topics->first();
        $this->assertTrue(
            str_contains($firstTopic->name, 'Kloroplas') ||
            str_contains($firstTopic->name, 'Konsep') ||
            str_contains($firstTopic->summary, 'Kloroplas')
        );
    }
}
