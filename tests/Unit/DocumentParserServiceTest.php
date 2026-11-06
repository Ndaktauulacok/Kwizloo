<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\DocumentParserService;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;

class DocumentParserServiceTest extends TestCase
{
    private DocumentParserService $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new DocumentParserService;
    }

    public function test_can_extract_text_from_plain_text_file(): void
    {
        $content = 'Fotosintesis berlaku dalam kloroplas.';
        $file = UploadedFile::fake()->createWithContent('nota.txt', $content);

        $extracted = $this->parser->extractTextFromUploadedFile($file);
        $this->assertEquals($content, $extracted);
    }

    public function test_rejects_disallowed_file_extension(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $file = UploadedFile::fake()->create('exploit.php', 10);
        $this->parser->extractTextFromUploadedFile($file);
    }
}
