<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\StudyDeckGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeckViewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_create_page(): void
    {
        $response = $this->get(route('decks.create'));
        $response->assertOk()
            ->assertSee('Masukkan Nota Pembelajaran')
            ->assertSee('Gunakan Nota Contoh');
    }

    public function test_can_view_study_hub_with_recommended_mission(): void
    {
        $deck = app(StudyDeckGeneratorService::class)->createFromSample();
        $response = $this->get(route('decks.show', $deck));

        $response->assertOk()
            ->assertSee($deck->title)
            ->assertSee('Misi Cadangan')
            ->assertSee('Penguasaan Topik')
            ->assertSee('Laporan Guru');
    }
}
