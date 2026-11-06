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
            ->assertSee('Maskot KwizLoo', false);
    }
}
