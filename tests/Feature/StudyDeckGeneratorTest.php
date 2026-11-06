<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\StudyDeck;
use App\Services\StudyDeckGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyDeckGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_sample_deck_with_3_topics_15_questions_12_flashcards(): void
    {
        $service = app(StudyDeckGeneratorService::class);
        $deck = $service->createFromSample();

        $this->assertInstanceOf(StudyDeck::class, $deck);
        $this->assertEquals(3, $deck->topics()->count());
        $this->assertEquals(15, $deck->questions()->count());
        $this->assertEquals(12, $deck->flashcards()->count());

        $firstTopic = $deck->topics()->first();
        $this->assertNotEmpty($firstTopic->name);
        $this->assertEquals(5, $firstTopic->questions()->count());
        $this->assertEquals(4, $firstTopic->flashcards()->count());
    }

    public function test_can_generate_deck_from_custom_source_text(): void
    {
        $service = app(StudyDeckGeneratorService::class);
        $text = 'Fotosintesis berlaku dalam kloroplas. Kloroplas mengandungi pigmen klorofil. Air diserap oleh akar.';
        $deck = $service->createFromText('Nota Ujian', $text);

        $this->assertInstanceOf(StudyDeck::class, $deck);
        $this->assertEquals('Nota Ujian', $deck->title);
        $this->assertGreaterThanOrEqual(1, $deck->topics()->count());
    }
}
