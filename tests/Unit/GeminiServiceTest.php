<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\GeminiService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiServiceTest extends TestCase
{
    public function test_generate_text_returns_ai_response_when_api_key_configured(): void
    {
        config(['services.gemini.key' => 'mock-gemini-key']);

        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Petunjuk: Perhatikan perkaitan cahaya.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(GeminiService::class);
        $result = $service->generateText('Beri petunjuk untuk soalan Fotosintesis.');

        $this->assertEquals('Petunjuk: Perhatikan perkaitan cahaya.', $result);
    }

    public function test_generate_structured_parses_json_schema(): void
    {
        config(['services.gemini.key' => 'mock-gemini-key']);

        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => json_encode(['topics' => [['name' => 'Klorofil', 'summary' => 'Pigmen hijau']]])],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(GeminiService::class);
        $result = $service->generateStructured('Senaraikan topik', [
            'type' => 'OBJECT',
            'properties' => [
                'topics' => ['type' => 'ARRAY'],
            ],
        ]);

        $this->assertArrayHasKey('topics', $result);
        $this->assertEquals('Klorofil', $result['topics'][0]['name']);
    }

    public function test_falls_back_cleanly_when_api_key_empty(): void
    {
        config(['services.gemini.key' => '']);

        $service = app(GeminiService::class);
        $this->assertFalse($service->isConfigured());

        $text = $service->generateText('Ujian');
        $this->assertNotEmpty($text);
    }

    public function test_generate_structured_falls_back_on_http_error(): void
    {
        config(['services.gemini.key' => 'mock-gemini-key']);

        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response(['error' => 'Rate limit exceeded'], 429),
        ]);

        $service = app(GeminiService::class);
        $result = $service->generateStructured('Senaraikan topik', [
            'type' => 'OBJECT',
            'properties' => [
                'topics' => ['type' => 'ARRAY'],
            ],
        ]);

        $this->assertIsArray($result);
    }

    public function test_openai_compatible_endpoint_generates_text_and_structured(): void
    {
        config([
            'services.gemini.key' => 'mock-9router-key',
            'services.gemini.model' => 'instant-kill',
            'services.gemini.base_url' => 'http://127.0.0.1:20128/v1',
        ]);

        Http::fake([
            'http://127.0.0.1:20128/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode(['topics' => [['name' => 'Fotosintesis', 'summary' => 'Proses tumbuhan']]]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(GeminiService::class);
        $this->assertTrue($service->isOpenAiCompatible());

        $result = $service->generateStructured('Uji prompt', [
            'type' => 'OBJECT',
            'properties' => ['topics' => ['type' => 'ARRAY']],
        ]);

        $this->assertArrayHasKey('topics', $result);
        $this->assertEquals('Fotosintesis', $result['topics'][0]['name']);
    }
}
