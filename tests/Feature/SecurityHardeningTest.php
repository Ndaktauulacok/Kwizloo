<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_returns_security_headers(): void
    {
        $response = $this->get(route('decks.create'));
        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_rate_limiting_protects_note_creation_endpoint(): void
    {
        // First 15 requests succeed (or redirect on sample)
        for ($i = 0; $i < 15; $i++) {
            $res = $this->post(route('decks.store'), ['use_sample' => 1]);
            $this->assertTrue(in_array($res->status(), [302, 200]));
        }

        // 16th request gets throttled (429 Too Many Requests)
        $throttled = $this->post(route('decks.store'), ['use_sample' => 1]);
        $throttled->assertStatus(429);
    }
}
