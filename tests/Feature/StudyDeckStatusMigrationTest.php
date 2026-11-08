<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\StudyDeck;
use App\Models\TeacherAiReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyDeckStatusMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_study_deck_has_status_progress_and_teacher_report_relation(): void
    {
        $deck = StudyDeck::create([
            'title' => 'Test Deck',
            'source_text' => 'Sample content',
            'status' => 'pending',
            'progress' => 10,
        ]);

        $this->assertEquals('pending', $deck->status);
        $this->assertEquals(10, $deck->progress);

        $report = TeacherAiReport::create([
            'study_deck_id' => $deck->id,
            'status' => 'completed',
            'summary' => 'Ringkasan kefahaman murid.',
            'misconceptions' => ['Konsep X keliru'],
            'recommendations' => ['Latihan topik X'],
        ]);

        $this->assertInstanceOf(TeacherAiReport::class, $deck->fresh()->teacherAiReport);
        $this->assertEquals('completed', $deck->fresh()->teacherAiReport->status);
    }
}
