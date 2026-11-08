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

    private ?string $baseUrl;

    private int $timeout;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.key', '');
        $this->model = (string) config('services.gemini.model', 'gemini-2.5-flash');
        $this->baseUrl = config('services.gemini.base_url') ? (string) config('services.gemini.base_url') : null;
        $this->timeout = (int) config('services.gemini.timeout', 60);
    }

    public function isConfigured(): bool
    {
        return trim($this->apiKey) !== '';
    }

    public function isOpenAiCompatible(): bool
    {
        return ! empty($this->baseUrl);
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
            if ($this->isOpenAiCompatible()) {
                $url = rtrim((string) $this->baseUrl, '/').'/chat/completions';
                $messages = [];
                if ($systemInstruction !== null && $systemInstruction !== '') {
                    $messages[] = ['role' => 'system', 'content' => $systemInstruction];
                }
                $messages[] = ['role' => 'user', 'content' => $prompt];

                $response = Http::timeout($timeout)
                    ->withHeaders([
                        'Authorization' => 'Bearer '.$this->apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->post($url, [
                        'model' => $this->model,
                        'messages' => $messages,
                        'stream' => false,
                    ]);
            } else {
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
            }

            if ($response->successful()) {
                $data = $response->json();
                if ($this->isOpenAiCompatible()) {
                    $text = (string) ($data['choices'][0]['message']['content'] ?? '');
                } else {
                    $text = (string) ($data['candidates'][0]['content']['parts'][0]['text'] ?? '');
                }

                if (trim($text) !== '') {
                    return trim($text);
                }
            }

            $friendlyErr = $this->formatFriendlyErrorMessage($response->status(), $response->body());
            Log::warning("Gemini API call failed with HTTP status {$response->status()}: {$response->body()}");

            if ($throwOnError) {
                throw new GeminiApiException($friendlyErr);
            }

            return $this->fallbackText($prompt);
        } catch (Throwable $e) {
            Log::error('Gemini text generation failed: '.$e->getMessage());

            if ($throwOnError) {
                if ($e instanceof GeminiApiException) {
                    throw $e;
                }
                throw new GeminiApiException($this->formatFriendlyErrorMessage(500, $e->getMessage()), 0, $e);
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
            if ($this->isOpenAiCompatible()) {
                $url = rtrim((string) $this->baseUrl, '/').'/chat/completions';
                $messages = [];
                $systemPrompt = ($systemInstruction !== null && $systemInstruction !== '')
                    ? $systemInstruction."\n\nAnda WAJIB membalas dengan objek JSON yang sah mengikut skema berikut:\n".json_encode($schema, JSON_PRETTY_PRINT)
                    : "Anda WAJIB membalas dengan objek JSON yang sah mengikut skema berikut:\n".json_encode($schema, JSON_PRETTY_PRINT);

                $messages[] = ['role' => 'system', 'content' => $systemPrompt];
                $messages[] = ['role' => 'user', 'content' => $prompt];

                $response = Http::timeout($timeout)
                    ->withHeaders([
                        'Authorization' => 'Bearer '.$this->apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->post($url, [
                        'model' => $this->model,
                        'messages' => $messages,
                        'response_format' => ['type' => 'json_object'],
                        'stream' => false,
                    ]);
            } else {
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
            }

            if ($response->successful()) {
                $data = $response->json();
                if ($this->isOpenAiCompatible()) {
                    $rawText = (string) ($data['choices'][0]['message']['content'] ?? '');
                } else {
                    $rawText = (string) ($data['candidates'][0]['content']['parts'][0]['text'] ?? '');
                }

                $cleanJson = $this->stripMarkdownFences($rawText);
                $decoded = json_decode($cleanJson, true);

                if (is_array($decoded)) {
                    return $decoded;
                }

                $parseErrMsg = 'AI structured output could not be parsed as valid JSON: '.json_last_error_msg();
                Log::warning($parseErrMsg);

                if ($throwOnError) {
                    throw new GeminiApiException($parseErrMsg);
                }
            } else {
                $friendlyErr = $this->formatFriendlyErrorMessage($response->status(), $response->body());
                Log::warning("Gemini API structured call failed with HTTP status {$response->status()}: {$response->body()}");

                if ($throwOnError) {
                    throw new GeminiApiException($friendlyErr);
                }
            }

            return $this->fallbackStructured($prompt, $schema);
        } catch (Throwable $e) {
            Log::error('Gemini structured generation failed: '.$e->getMessage());

            if ($throwOnError) {
                if ($e instanceof GeminiApiException) {
                    throw $e;
                }
                throw new GeminiApiException($this->formatFriendlyErrorMessage(500, $e->getMessage()), 0, $e);
            }

            return $this->fallbackStructured($prompt, $schema);
        }
    }

    public function formatFriendlyErrorMessage(int $status, string $rawBody): string
    {
        $lower = mb_strtolower($rawBody);

        if ($status === 429 || str_contains($lower, 'quota') || str_contains($lower, 'resource_exhausted')) {
            return 'Had kuota harian Gemini AI (Free Tier) telah dicapai (20 permintaan sehari). Sila cuba lagi kemudian atau bina modul dalam mod asas.';
        }

        if ($status === 401 || $status === 403 || str_contains($lower, 'api key not valid')) {
            return 'Kunci Gemini API tidak sah atau tiada kebenaran akses.';
        }

        if ($status >= 500) {
            return 'Pelayan Google Gemini mengalami gangguan teknikal sementara. Sila cuba sebentar lagi.';
        }

        $decoded = json_decode($rawBody, true);
        if (is_array($decoded) && ! empty($decoded['error']['message']) && is_string($decoded['error']['message'])) {
            $msg = trim($decoded['error']['message']);
            if (str_contains(mb_strtolower($msg), 'quota')) {
                return 'Had kuota harian Gemini AI telah dicapai. Sila cuba lagi kemudian.';
            }

            return $msg;
        }

        return 'Ralat komunikasi dengan perkhidmatan AI (Status: '.$status.').';
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
