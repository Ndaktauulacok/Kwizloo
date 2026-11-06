<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Smalot\PdfParser\Parser as PdfParser;
use Throwable;

class DocumentParserService
{
    public function __construct(
        private readonly ContentValidationService $validation = new ContentValidationService,
        private readonly PdfParser $pdfParser = new PdfParser,
    ) {}

    /**
     * Extract text from uploaded PDF or TXT file safely.
     *
     * @throws InvalidArgumentException
     */
    public function extractTextFromUploadedFile(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();

        if ($extension === 'txt' || $mime === 'text/plain') {
            $raw = file_get_contents($file->getRealPath());
            if ($raw === false) {
                throw new InvalidArgumentException('Gagal membaca fail teks.');
            }

            return $this->validation->sanitizeText($raw);
        }

        if ($extension === 'pdf' || $mime === 'application/pdf') {
            try {
                $pdf = $this->pdfParser->parseFile($file->getRealPath());
                $text = $pdf->getText();
                $clean = $this->validation->sanitizeText($text);

                if (mb_strlen($clean) < 50) {
                    throw new InvalidArgumentException('PDF ini tiada lapisan teks yang boleh diekstrak. Sila gunakan teks biasa.');
                }

                return $clean;
            } catch (Throwable $e) {
                if ($e instanceof InvalidArgumentException) {
                    throw $e;
                }
                throw new InvalidArgumentException('Fail PDF tidak sah atau rosak.');
            }
        }

        throw new InvalidArgumentException('Hanya format fail PDF dan TXT disokong.');
    }
}
