<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\StudyDeckGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_teacher_diagnostic_report(): void
    {
        $deck = app(StudyDeckGeneratorService::class)->createFromSample();
        $response = $this->get(route('decks.report', $deck));

        $response->assertOk()
            ->assertSee('Laporan Diagnostik Pembelajaran')
            ->assertSee('Analisis Topik')
            ->assertSee('Pelan Tindakan Ejen');
    }
}
