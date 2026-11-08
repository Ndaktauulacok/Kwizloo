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

    public function test_history_shows_an_empty_state_without_notes(): void
    {
        $this->get(route('decks.index'))
            ->assertOk()
            ->assertSee('Belum ada nota diproses');
    }
}
