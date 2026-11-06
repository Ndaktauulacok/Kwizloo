<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\StudyDeck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyDeckModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_study_deck_with_topics_questions_and_flashcards(): void
    {
        $deck = StudyDeck::create([
            'title' => 'Fotosintesis Tingkatan 4',
            'source_text' => 'Fotosintesis ialah proses tumbuhan membuat makanan.',
            'total_words' => 7,
        ]);

        $topic = $deck->topics()->create([
            'name' => 'Bahan dan Hasil',
            'summary' => 'Keperluan fotosintesis',
            'status' => 'active',
            'hp' => 3,
            'max_hp' => 3,
            'strategy' => ['level' => 2, 'hint' => false, 'note' => 'Permulaan'],
        ]);

        $question = $topic->questions()->create([
            'study_deck_id' => $deck->id,
            'question' => 'Apakah hasil fotosintesis?',
            'options' => ['Glukosa dan oksigen', 'Air dan kanji', 'Karbon dioksida', 'Klorofil'],
            'correct_index' => 0,
            'difficulty' => 1,
            'explanation' => 'Tumbuhan menghasilkan glukosa dan membebaskan oksigen.',
            'quote' => 'tumbuhan membuat makanan',
        ]);

        $flashcard = $topic->flashcards()->create([
            'study_deck_id' => $deck->id,
            'front' => 'Hasil fotosintesis',
            'back' => 'Glukosa dan oksigen',
            'quote' => 'membuat makanan',
        ]);

        $this->assertDatabaseHas('study_decks', ['id' => $deck->id, 'title' => 'Fotosintesis Tingkatan 4']);
        $this->assertCount(1, $deck->topics);
        $this->assertCount(1, $deck->questions);
        $this->assertCount(1, $deck->flashcards);
        $this->assertTrue($question->isCorrect(0));
        $this->assertFalse($question->isCorrect(1));
    }
}
