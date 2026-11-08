<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\GenerateTeacherReportJob;
use App\Models\StudyDeck;
use App\Models\TeacherAiReport;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TeacherAiReportGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_generate_teacher_ai_report_without_sessions(): void
    {
        $deck = StudyDeck::create(['title' => 'Sains', 'source_text' => 'Nota...', 'status' => 'completed']);

        $response = $this->postJson(route('decks.report.generate', $deck));

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Laporan diagnostik AI memerlukan sekurang-kurangnya satu sesi aktiviti pelajar.',
            ]);
    }

    public function test_dispatches_generate_teacher_report_job_when_sessions_exist(): void
    {
        Queue::fake();

        $deck = StudyDeck::create(['title' => 'Sains', 'source_text' => 'Nota...', 'status' => 'completed']);
        $deck->studySessions()->create([
            'activity_type' => 'blitz',
            'score' => 80,
            'right_count' => 8,
            'wrong_count' => 2,
            'best_combo' => 4,
        ]);

        $response = $this->postJson(route('decks.report.generate', $deck));

        $response->assertOk()
            ->assertJson(['status' => 'pending']);

        Queue::assertPushed(GenerateTeacherReportJob::class, function ($job) use ($deck) {
            return $job->deck->id === $deck->id;
        });
    }

    public function test_generate_teacher_report_job_creates_ai_report(): void
    {
        $deck = StudyDeck::create(['title' => 'Fizik SPM', 'source_text' => 'Nota fizik...', 'status' => 'completed']);
        $deck->studySessions()->create([
            'activity_type' => 'boss',
            'score' => 50,
            'right_count' => 3,
            'wrong_count' => 5,
            'best_combo' => 2,
        ]);

        $this->mock(GeminiService::class, function ($mock) {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('generateStructured')->andReturn([
                'summary' => 'Murid menghadapi kesukaran dalam pengiraan halaju.',
                'misconceptions' => ['Kekeliruan unit m/s dan km/j'],
                'recommendations' => ['Adakan sesi tutorial format graf halaju-masa'],
            ]);
        });

        $job = new GenerateTeacherReportJob($deck);
        $job->handle(app(GeminiService::class));

        $report = TeacherAiReport::where('study_deck_id', $deck->id)->first();
        $this->assertNotNull($report);
        $this->assertEquals('completed', $report->status);
        $this->assertStringContainsString('pengiraan halaju', $report->summary);
        $this->assertCount(1, $report->misconceptions);
        $this->assertCount(1, $report->recommendations);
    }

    public function test_report_status_endpoint_returns_json(): void
    {
        $deck = StudyDeck::create(['title' => 'Sains', 'source_text' => 'Nota...', 'status' => 'completed']);
        $deck->teacherAiReport()->create([
            'status' => 'completed',
            'summary' => 'Ringkasan laporan.',
            'misconceptions' => ['Silap A'],
            'recommendations' => ['Ulang B'],
        ]);

        $response = $this->getJson(route('decks.report.status', $deck));

        $response->assertOk()
            ->assertJson([
                'status' => 'completed',
                'summary' => 'Ringkasan laporan.',
            ]);
    }
}
