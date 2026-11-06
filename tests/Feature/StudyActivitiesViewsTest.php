<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\StudyDeckGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyActivitiesViewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_9_study_activities_render_successfully(): void
    {
        $deck = app(StudyDeckGeneratorService::class)->createFromSample();
        $activities = ['boss', 'blitz', 'rain', 'match', 'tower', 'tf', 'word', 'cards', 'race'];

        foreach ($activities as $act) {
            $response = $this->get(route('decks.activities.show', [$deck, $act]));
            $response->assertOk();
        }
    }
}
