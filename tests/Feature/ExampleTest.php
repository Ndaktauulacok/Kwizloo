<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The root URL shows the landing page with a get-started link to the note intake page.
     */
    public function test_home_shows_landing_page_with_get_started_link(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Mulakan Sekarang')
            ->assertSee(route('decks.create'), false)
            ->assertSee('Maskot KwizLoo', false)
            ->assertSee(route('how-it-works'), false)
            ->assertDontSee('Tiga langkah mudah');
    }

    /**
     * The how-it-works page explains the steps and links back to get started.
     */
    public function test_how_it_works_page_explains_steps_and_links_to_get_started(): void
    {
        $response = $this->get(route('how-it-works'));

        $response->assertOk()
            ->assertSee('Cara KwizLoo')
            ->assertSee('Masukkan nota')
            ->assertSee('9 mod pembelajaran')
            ->assertSee(route('decks.create'), false)
            ->assertSee(route('home'), false);
    }
}
