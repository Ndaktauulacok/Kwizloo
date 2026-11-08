<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\StudyDeck;
use App\Services\AdaptiveAgentService;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdaptiveAgentServiceAiTest extends TestCase
{
    use RefreshDatabase;

    public function test_determine_next_mission_enriches_rationale_with_gemini_when_available(): void
    {
        Cache::flush();

        $deck = StudyDeck::create([
            'title' => 'Biologi Tingkatan 4',
            'source_text' => 'Teks biologi...',
            'status' => 'completed',
        ]);

        $topic = $deck->topics()->create([
            'name' => 'Respirasi Sel',
            'summary' => 'Ringkasan respirasi',
            'status' => 'boss',
            'hp' => 3,
            'max_hp' => 3,
            'right_count' => 1,
            'wrong_count' => 4,
            'mistakes' => ['Apakah fungsi mitokondria?'],
        ]);

        $mockGemini = $this->createMock(GeminiService::class);
        $mockGemini->method('isConfigured')->willReturn(true);
        $mockGemini->method('generateText')->willReturn('Analisis AI: Pelajar keliru antara glikolisis dan kitaran Krebs. Latihan kad imbasan disyorkan.');

        $service = new AdaptiveAgentService($mockGemini);
        $mission = $service->determineNextMission($deck);

        $this->assertEquals('cards', $mission['activity']);
        $this->assertStringContainsString('Analisis AI:', $mission['rationale']);
    }

    public function test_determine_next_mission_falls_back_gracefully_when_gemini_unconfigured(): void
    {
        Cache::flush();

        $deck = StudyDeck::create([
            'title' => 'Fizik Tingkatan 4',
            'source_text' => 'Teks fizik...',
            'status' => 'completed',
        ]);

        $mockGemini = $this->createMock(GeminiService::class);
        $mockGemini->method('isConfigured')->willReturn(false);

        $service = new AdaptiveAgentService($mockGemini);
        $mission = $service->determineNextMission($deck);

        $this->assertNotEmpty($mission['activity']);
        $this->assertNotEmpty($mission['rationale']);
    }
}
