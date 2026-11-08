<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeminiService
{
    private string $apiKey;

    private string $model;

    private int $timeout;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.key', '');
        $this->model = (string) config('services.gemini.model', 'gemini-2.5-flash');
        $this->timeout = (int) config('services.gemini.timeout', 30);
    }

    public function isConfigured(): bool
    {
        return trim($this->apiKey) !== '';
    }

    /**
     * Generate plain text or explanation using Gemini API with deterministic fallback.
     */
    public function generateText(
        string $prompt,
        ?string $systemInstruction = null,
        bool $throwOnError = false,
        ?int $timeoutOverride = null,
    ): string {
        if (! $this->isConfigured()) {
            return $this->fallbackText($prompt);
        }

        $timeout = $timeoutOverride ?? $this->timeout;

        try {
            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ];

            if ($systemInstruction !== null && $systemInstruction !== '') {
                $payload['system_instruction'] = [
                    'parts' => [
                        ['text' => $systemInstruction],
                    ],
                ];
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout($timeout)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $text = (string) ($data['candidates'][0]['content']['parts'][0]['text'] ?? '');
                if (trim($text) !== '') {
                    return trim($text);
                }
            }

            $errMsg = "Gemini API call failed with HTTP status {$response->status()}: {$response->body()}";
            Log::warning($errMsg);

            if ($throwOnError) {
                throw new GeminiApiException($errMsg);
            }

            return $this->fallbackText($prompt);
        } catch (Throwable $e) {
            Log::error('Gemini text generation failed: '.$e->getMessage());

            if ($throwOnError) {
                if ($e instanceof GeminiApiException) {
                    throw $e;
                }
                throw new GeminiApiException('Gemini text generation error: '.$e->getMessage(), 0, $e);
            }

            return $this->fallbackText($prompt);
        }
    }

    /**
     * Generate structured JSON data strictly adhering to a given JSON Schema with fallback.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public function generateStructured(
        string $prompt,
        array $schema,
        ?string $systemInstruction = null,
        bool $throwOnError = false,
        ?int $timeoutOverride = null,
    ): array {
        if (! $this->isConfigured()) {
            return $this->fallbackStructured($prompt, $schema);
        }

        $timeout = $timeoutOverride ?? $this->timeout;

        try {
            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'responseSchema' => $schema,
                ],
            ];

            if ($systemInstruction !== null && $systemInstruction !== '') {
                $payload['system_instruction'] = [
                    'parts' => [
                        ['text' => $systemInstruction],
                    ],
                ];
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout($timeout)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $rawText = (string) ($data['candidates'][0]['content']['parts'][0]['text'] ?? '');
                $cleanJson = $this->stripMarkdownFences($rawText);
                $decoded = json_decode($cleanJson, true);

                if (is_array($decoded)) {
                    return $decoded;
                }

                $parseErrMsg = 'Gemini structured output could not be parsed as valid JSON: '.json_last_error_msg();
                Log::warning($parseErrMsg);

                if ($throwOnError) {
                    throw new GeminiApiException($parseErrMsg);
                }
            } else {
                $httpErrMsg = "Gemini API structured call failed with HTTP status {$response->status()}: {$response->body()}";
                Log::warning($httpErrMsg);

                if ($throwOnError) {
                    throw new GeminiApiException($httpErrMsg);
                }
            }

            return $this->fallbackStructured($prompt, $schema);
        } catch (Throwable $e) {
            Log::error('Gemini structured generation failed: '.$e->getMessage());

            if ($throwOnError) {
                if ($e instanceof GeminiApiException) {
                    throw $e;
                }
                throw new GeminiApiException('Gemini structured generation error: '.$e->getMessage(), 0, $e);
            }

            return $this->fallbackStructured($prompt, $schema);
        }
    }

    private function stripMarkdownFences(string $text): string
    {
        $clean = trim($text);
        if (str_starts_with($clean, '```json')) {
            $clean = substr($clean, 7);
        } elseif (str_starts_with($clean, '```')) {
            $clean = substr($clean, 3);
        }

        if (str_ends_with($clean, '```')) {
            $clean = substr($clean, 0, -3);
        }

        return trim($clean);
    }

    private function fallbackText(string $prompt): string
    {
        if (str_contains(mb_strtolower($prompt), 'petunjuk') || str_contains(mb_strtolower($prompt), 'hint')) {
            return 'Petunjuk AI: Kenal pasti kata kunci utama dalam petikan rujukan untuk mencari hubung kait konsep.';
        }

        if (str_contains(mb_strtolower($prompt), 'terang') || str_contains(mb_strtolower($prompt), 'explain')) {
            return 'Pilihan ini tidak selari dengan maklumat fakta dalam teks rujukan. Semak semula definisi konsep asas.';
        }

        return 'Analisis adaptif disimulasikan: Teruskan latihan aktif untuk memperkukuhkan pemahaman.';
    }

    /**
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    private function fallbackStructured(string $prompt, array $schema): array
    {
        if (isset($schema['properties']['topics'])) {
            // Return empty topics so that caller executes grounded heuristic extraction from real source text
            return ['topics' => []];
        }

        if (isset($schema['properties']['summary'])) {
            return [
                'summary' => 'Secara keseluruhannya, pelajar menunjukkan kefahaman asas yang baik, namun memerlukan latihan pengukuhan dalam soalan aras tinggi.',
                'misconceptions' => [
                    'Kekeliruan antara istilah utama dan fungsinya.',
                ],
                'recommendations' => [
                    'Lakukan aktiviti Flashcard Berjarak sebelum menjawab Penilaian Penguasaan.',
                    'Fokus kepada padanan istilah sains utama.',
                ],
            ];
        }

        return [];
    }
}
