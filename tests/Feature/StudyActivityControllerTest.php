<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\StudyDeck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyActivityControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_verify_answer_validates_correctly_and_updates_topic_stats(): void
    {
        $deck = StudyDeck::create(['title' => 'Biology', 'source_text' => 'Source', 'total_words' => 50]);
        $topic = $deck->topics()->create([
            'name' => 'Cell Structure',
            'summary' => 'Summary',
            'status' => 'boss',
            'hp' => 3,
            'max_hp' => 3,
            'strategy' => ['level' => 2, 'hint' => false, 'note' => ''],
        ]);

        $question = $topic->questions()->create([
            'study_deck_id' => $deck->id,
            'question' => 'Where is DNA stored?',
            'options' => ['Nucleus', 'Mitochondria', 'Vacuole', 'Ribosome'],
            'correct_index' => 0,
            'difficulty' => 1,
            'explanation' => 'DNA is held in the nucleus.',
            'quote' => 'stored in the nucleus',
        ]);

        // Submit correct answer
        $response = $this->postJson(route('decks.verify-answer', $deck), [
            'question_id' => $question->id,
            'selected_index' => 0,
            'is_boss' => true,
        ]);

        $response->assertOk()
            ->assertJson([
                'is_correct' => true,
                'explanation' => 'DNA is held in the nucleus.',
                'quote' => 'stored in the nucleus',
            ]);

        $this->assertEquals(2, $topic->fresh()->hp);
        $this->assertEquals(1, $topic->fresh()->right_count);

        // Submit wrong answer
        $responseWrong = $this->postJson(route('decks.verify-answer', $deck), [
            'question_id' => $question->id,
            'selected_index' => 1,
            'is_boss' => true,
        ]);

        $responseWrong->assertOk()->assertJson(['is_correct' => false]);
        $this->assertEquals(1, $topic->fresh()->wrong_count);
    }

    public function test_verify_answer_rejects_tampered_payload(): void
    {
        $deck = StudyDeck::create(['title' => 'Bio', 'source_text' => 'Src', 'total_words' => 10]);
        $response = $this->postJson(route('decks.verify-answer', $deck), [
            'question_id' => 999999, // non-existent
            'selected_index' => 99,   // out of bounds
        ]);

        $response->assertUnprocessable();
    }
}
