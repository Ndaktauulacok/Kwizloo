<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\StudyDeck;
use App\Services\StudyDeckGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeckHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_lists_processed_notes_with_links_back_to_them(): void
    {
        $readyDeck = app(StudyDeckGeneratorService::class)->createFromSample();
        $pendingDeck = StudyDeck::create([
            'title' => 'Nota Sejarah Melaka',
            'source_text' => 'Kesultanan Melayu Melaka diasaskan oleh Parameswara.',
            'status' => 'processing',
            'progress' => 40,
        ]);

        $this->get(route('decks.index'))
            ->assertOk()
            ->assertSee('Sejarah Nota')
            ->assertSee($readyDeck->title)
            ->assertSee(route('decks.show', $readyDeck), false)
            ->assertSee($pendingDeck->title)
            ->assertSee(route('decks.processing', $pendingDeck), false);
    }

    public function test_history_search_filters_by_title(): void
    {
        StudyDeck::create(['title' => 'Fizik Newton', 'source_text' => 'Hukum gerakan.', 'status' => 'completed']);
        StudyDeck::create(['title' => 'Biologi Sel', 'source_text' => 'Organel sel.', 'status' => 'completed']);

        $this->get(route('decks.index', ['q' => 'Newton']))
            ->assertOk()
            ->assertSee('Fizik Newton')
            ->assertDontSee('Biologi Sel');
    }

    public function test_deleting_a_note_removes_it_and_everything_generated_from_it(): void
    {
        $deck = app(StudyDeckGeneratorService::class)->createFromSample();
        $deck->studySessions()->create([
            'activity_type' => 'blitz',
            'score' => 50,
            'right_count' => 5,
            'wrong_count' => 5,
            'best_combo' => 2,
        ]);

        $this->delete(route('decks.destroy', $deck))
            ->assertRedirect(route('decks.index'))
            ->assertSessionHas('status');

        $this->assertModelMissing($deck);
        $this->assertDatabaseMissing('topics', ['study_deck_id' => $deck->id]);
        $this->assertDatabaseMissing('questions', ['study_deck_id' => $deck->id]);
        $this->assertDatabaseMissing('flashcards', ['study_deck_id' => $deck->id]);
        $this->assertDatabaseMissing('study_sessions', ['study_deck_id' => $deck->id]);
    }

    public function test_deleting_keeps_the_current_search(): void
    {
        $deck = StudyDeck::create(['title' => 'Fizik Newton', 'source_text' => 'Hukum gerakan.', 'status' => 'completed']);

        $this->delete(route('decks.destroy', $deck), ['q' => 'Fizik'])
            ->assertRedirect(route('decks.index', ['q' => 'Fizik']));
    }

    public function test_history_page_offers_a_delete_confirmation(): void
    {
        app(StudyDeckGeneratorService::class)->createFromSample();

        $this->get(route('decks.index'))
            ->assertOk()
            ->assertSee('Padam nota ini?')
            ->assertSee('Tindakan ini tidak boleh dibatalkan.');
    }

    public function test_history_shows_an_empty_state_without_notes(): void
    {
        $this->get(route('decks.index'))
            ->assertOk()
            ->assertSee('Belum ada nota diproses');
    }
}
