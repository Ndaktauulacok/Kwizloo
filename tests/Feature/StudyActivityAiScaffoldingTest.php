<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\StudyDeck;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyActivityAiScaffoldingTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_hint_returns_contextual_hint_without_revealing_answer(): void
    {
        $deck = StudyDeck::create(['title' => 'Fizik', 'source_text' => 'Hukum Newton...', 'status' => 'completed']);
        $topic = $deck->topics()->create(['name' => 'Daya', 'summary' => 'Hukum Gerakan']);
        $question = $topic->questions()->create([
            'study_deck_id' => $deck->id,
            'question' => 'Apakah unit bagi daya?',
            'options' => ['Newton', 'Joule', 'Watt', 'Pascal'],
            'correct_index' => 0,
            'difficulty' => 1,
            'explanation' => 'Daya diukur dalam Newton.',
            'quote' => 'Unit SI bagi daya ialah Newton (N).',
        ]);

        $this->mock(GeminiService::class, function ($mock) {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('generateText')->andReturn('Fikirkan tentang saintis yang menemui graviti.');
        });

        $response = $this->postJson(route('decks.ai-hint', $deck), [
            'question_id' => $question->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'hint' => 'Fikirkan tentang saintis yang menemui graviti.',
            ]);
    }

    public function test_ai_explain_provides_targeted_feedback_for_mistake(): void
    {
        $deck = StudyDeck::create(['title' => 'Fizik', 'source_text' => 'Hukum Newton...', 'status' => 'completed']);
        $topic = $deck->topics()->create(['name' => 'Daya', 'summary' => 'Hukum Gerakan']);
        $question = $topic->questions()->create([
            'study_deck_id' => $deck->id,
            'question' => 'Apakah unit bagi daya?',
            'options' => ['Newton', 'Joule', 'Watt', 'Pascal'],
            'correct_index' => 0,
            'difficulty' => 1,
            'explanation' => 'Daya diukur dalam Newton.',
            'quote' => 'Unit SI bagi daya ialah Newton (N).',
        ]);

        $this->mock(GeminiService::class, function ($mock) {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('generateText')->andReturn('Joule ialah unit bagi tenaga, bukan daya.');
        });

        $response = $this->postJson(route('decks.ai-explain', $deck), [
            'question_id' => $question->id,
            'selected_index' => 1,
        ]);

        $response->assertOk()
            ->assertJson([
                'explanation' => 'Joule ialah unit bagi tenaga, bukan daya.',
            ]);
    }
}
