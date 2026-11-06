<?php

declare(strict_types=1);

namespace App\Services;

class ContentValidationService
{
    /**
     * Normalize text by removing non-alphanumeric punctuation and lowercasing.
     */
    public function normalize(string $text): string
    {
        $normalized = mb_strtolower($text, 'UTF-8');
        $cleaned = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $normalized) ?? '';

        return trim($cleaned);
    }

    /**
     * Check if a quote exists verbatim inside the source text.
     */
    public function validateQuoteExists(string $sourceText, string $quote): bool
    {
        $normSource = $this->normalize($sourceText);
        $normQuote = $this->normalize($quote);

        if ($normQuote === '' || count(explode(' ', $normQuote)) < 2) {
            return false;
        }

        return str_contains($normSource, $normQuote);
    }

    /**
     * Sanitize text against XSS, null bytes, and non-printable controls.
     */
    public function sanitizeText(string $input): string
    {
        // Strip null bytes
        $clean = str_replace("\0", '', $input);

        // Strip HTML tags completely to prevent any script or malicious markup injection
        $clean = strip_tags($clean);

        // Normalize unicode whitespace
        $clean = preg_replace('/[^\P{C}\n\r\t]+/u', '', $clean) ?? '';

        return trim($clean);
    }

    /**
     * Validate raw question structure and ensure verbatim quote exists.
     * Returns error string or null if valid.
     */
    public function validateQuestion(array $q, string $sourceText): ?string
    {
        $question = trim((string) ($q['soalan'] ?? $q['question'] ?? ''));
        if ($question === '') {
            return 'Soalan tidak boleh kosong.';
        }

        $opts = (array) ($q['pilihan'] ?? $q['options'] ?? []);
        if (count($opts) !== 4) {
            return 'Soalan memerlukan tepat 4 pilihan jawapan.';
        }

        $normalizedOpts = array_map(fn ($o) => $this->normalize((string) $o), $opts);
        if (count(array_unique($normalizedOpts)) !== 4) {
            return 'Pilihan jawapan mesti berbeza antara satu sama lain.';
        }

        $ans = $q['jawapan'] ?? $q['correct_index'] ?? null;
        if (! is_int($ans) || $ans < 0 || $ans > 3) {
            return 'Indeks jawapan mesti integer antara 0 dan 3.';
        }

        $quote = trim((string) ($q['petikan'] ?? $q['quote'] ?? ''));
        if (! $this->validateQuoteExists($sourceText, $quote)) {
            return 'Petikan mesti wujud secara tepat dalam nota sumber.';
        }

        return null;
    }

    /**
     * Validate flashcard structure.
     */
    public function validateFlashcard(array $card, string $sourceText): ?string
    {
        $front = trim((string) ($card['depan'] ?? $card['front'] ?? ''));
        $back = trim((string) ($card['belakang'] ?? $card['back'] ?? ''));
        $quote = trim((string) ($card['petikan'] ?? $card['quote'] ?? ''));

        if ($front === '' || $back === '') {
            return 'Muka hadapan dan belakang flashcard tidak boleh kosong.';
        }

        if ($quote !== '' && ! $this->validateQuoteExists($sourceText, $quote)) {
            return 'Petikan kad mesti wujud dalam nota sumber.';
        }

        return null;
    }
}
