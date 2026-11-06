<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\ContentValidationService;
use PHPUnit\Framework\TestCase;

class ContentValidationServiceTest extends TestCase
{
    private ContentValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ContentValidationService;
    }

    public function test_verifies_verbatim_quote_exists_in_source_text(): void
    {
        $source = 'Fotosintesis ialah proses tumbuhan hijau membuat makanan sendiri.';
        $this->assertTrue($this->service->validateQuoteExists($source, 'tumbuhan hijau membuat makanan'));
        $this->assertFalse($this->service->validateQuoteExists($source, 'reaksi nuklear hidrogen'));
    }

    public function test_sanitizes_dangerous_html_and_null_bytes(): void
    {
        $dirty = "Nota <script>alert('xss')</script> tentang sel.\0";
        $clean = $this->service->sanitizeText($dirty);
        $this->assertStringNotContainsString('<script>', $clean);
        $this->assertStringNotContainsString("\0", $clean);
    }

    public function test_validates_question_has_four_distinct_options_and_valid_answer(): void
    {
        $source = 'Klorofil ialah pigmen hijau yang menyerap tenaga cahaya.';
        $validQ = [
            'soalan' => 'Apakah fungsi klorofil?',
            'pilihan' => ['Menyerap cahaya', 'Menyimpan kanji', 'Mengangkut air', 'Membuka stoma'],
            'jawapan' => 0,
            'tahap' => 1,
            'penjelasan' => 'Klorofil menyerap cahaya untuk fotosintesis.',
            'petikan' => 'pigmen hijau yang menyerap tenaga cahaya',
        ];

        $this->assertNull($this->service->validateQuestion($validQ, $source));

        $invalidQ = $validQ;
        $invalidQ['pilihan'] = ['Sama', 'Sama', 'Lain', 'Lagi'];
        $this->assertNotNull($this->service->validateQuestion($invalidQ, $source));
    }
}
