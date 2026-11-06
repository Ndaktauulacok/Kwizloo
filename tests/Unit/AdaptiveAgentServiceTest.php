<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\StudyDeck;
use App\Services\AdaptiveAgentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdaptiveAgentServiceTest extends TestCase
{
    use RefreshDatabase;

    private AdaptiveAgentService $agent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agent = new AdaptiveAgentService;
    }

    public function test_selects_flashcards_when_topic_has_low_accuracy_or_losses(): void
    {
        $deck = StudyDeck::create(['title' => 'Test', 'source_text' => 'Source', 'total_words' => 10]);
        $weakTopic = $deck->topics()->create([
            'name' => 'Topik Lemah',
            'summary' => 'Summary',
            'status' => 'boss',
            'hp' => 4,
            'max_hp' => 4,
            'right_count' => 1,
            'wrong_count' => 4,
            'losses' => 1,
        ]);

        $mission = $this->agent->determineNextMission($deck);
        $this->assertEquals($weakTopic->id, $mission['topic_id']);
        $this->assertEquals('cards', $mission['activity']);
        $this->assertStringContainsString('flashcard', strtolower($mission['rationale']));
    }

    public function test_lowers_difficulty_and_activates_hint_when_student_loses_to_boss(): void
    {
        $deck = StudyDeck::create(['title' => 'Test', 'source_text' => 'Source', 'total_words' => 10]);
        $topic = $deck->topics()->create([
            'name' => 'Fotosintesis',
            'summary' => 'Summary',
            'status' => 'boss',
            'hp' => 3,
            'max_hp' => 3,
            'losses' => 0,
            'strategy' => ['level' => 2, 'hint' => false, 'note' => 'Standard'],
        ]);

        $newStrategy = $this->agent->adjustStrategyAfterLoss($topic);
        $this->assertEquals(1, $newStrategy['level']);
        $this->assertTrue($newStrategy['hint']);
        $this->assertEquals(1, $topic->fresh()->losses);
    }
}
