# Gemini AI Integration Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Integrate Google Gemini AI across KwizLoo for grounded deck generation, asynchronous job processing with polling UI, adaptive learning recommendations, interactive quiz scaffolding, and diagnostic teacher reports.

**Architecture:** A centralized `GeminiService` wraps Gemini REST API (`gemini-2.5-flash`) with structured JSON schema output and deterministic fallback. Heavy tasks (`GenerateStudyDeckJob`, `GenerateTeacherReportJob`) run via Laravel's database queue with status polling (`GET /decks/{deck}/status`, `GET /decks/{deck}/report/status`). Interactive hints and explanations run on-demand.

**Tech Stack:** PHP 8.5, Laravel 13.x, MySQL, Google Gemini API, Tailwind CSS, Alpine.js / Vanilla JS.

**Spec:** `docs/superpowers/specs/2026-11-08-gemini-ai-integration-design.md`

## Global Constraints

- PHP Version: 8.3+ with strict types enabled (`declare(strict_types=1);`).
- API Provider: Google Gemini REST API (`gemini-2.5-flash`) using Laravel HTTP client.
- Database: MySQL (`kwizloo` / `kwizloo_test`).
- Queue: Database queue driver (`queue:work`) or `sync` for test execution.
- Grounding: Questions, answers, and flashcards must include direct source quotations.
- Resilience: All AI features must gracefully fall back to deterministic mocks when `GEMINI_API_KEY` is absent.

## Review Focus

1. **Missing `GEMINI_API_KEY` in environment**: System must seamlessly fall back to deterministic mocks without throwing exceptions.
2. **Gemini API timeout or 429 rate limit**: Jobs must record `failed` status and readable error message in DB rather than crashing silently.
3. **Invalid or malformed JSON from Gemini**: `GeminiService` must catch parse errors, log to `AgentLog`, and trigger fallback.
4. **Duplicate Teacher Report job dispatch on reload**: `Cache::lock` must prevent multiple simultaneous jobs for the same deck.
5. **Teacher report generation on deck with 0 sessions**: Controller must reject dispatch and UI must disable generate button with informative Malay message.

---

### Task 1: Environment Configuration, Database Migrations & Eloquent Models

**Files:**
- Modify: `config/services.php:30-40`
- Modify: `.env.example:60-65`
- Create: `database/migrations/2026_11_08_000001_add_status_and_progress_to_study_decks_table.php`
- Create: `database/migrations/2026_11_08_000002_create_teacher_ai_reports_table.php`
- Create: `app/Models/TeacherAiReport.php`
- Modify: `app/Models/StudyDeck.php:1-60`
- Test: `tests/Feature/StudyDeckStatusMigrationTest.php`

**Interfaces:**
- Produces: `StudyDeck::$status`, `StudyDeck::$progress`, `StudyDeck::$error_message`, `StudyDeck::teacherAiReport()`, `TeacherAiReport` model.

- [ ] **Step 1: Write the failing test**

```php
namespace Tests\Feature;

use App\Models\StudyDeck;
use App\Models\TeacherAiReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyDeckStatusMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_study_deck_has_status_progress_and_teacher_report_relation(): void
    {
        $deck = StudyDeck::create([
            'title' => 'Test Deck',
            'source_text' => 'Sample content',
            'status' => 'pending',
            'progress' => 10,
        ]);

        $this->assertEquals('pending', $deck->status);
        $this->assertEquals(10, $deck->progress);

        $report = TeacherAiReport::create([
            'study_deck_id' => $deck->id,
            'status' => 'completed',
            'summary' => 'Ringkasan kefahaman murid.',
            'misconceptions' => ['Konsep X keliru'],
            'recommendations' => ['Latihan topik X'],
        ]);

        $this->assertInstanceOf(TeacherAiReport::class, $deck->fresh()->teacherAiReport);
        $this->assertEquals('completed', $deck->fresh()->teacherAiReport->status);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Feature/StudyDeckStatusMigrationTest.php`
Expected: FAIL with missing column or missing `TeacherAiReport` class.

- [ ] **Step 3: Implement config, migrations, and models**

- Add `gemini` credentials block in `config/services.php` reading `GEMINI_API_KEY`, `GEMINI_MODEL`, `GEMINI_TIMEOUT`.
- Add `GEMINI_API_KEY=` and `GEMINI_MODEL=gemini-2.5-flash` to `.env.example`.
- Migration `2026_11_08_000001_add_status_and_progress_to_study_decks_table.php`:
  `$table->string('status', 30)->default('completed');`
  `$table->unsignedTinyInteger('progress')->default(100);`
  `$table->text('error_message')->nullable();`
- Migration `2026_11_08_000002_create_teacher_ai_reports_table.php`:
  `$table->foreignId('study_deck_id')->constrained()->cascadeOnDelete();`
  `$table->string('status', 30)->default('pending');`
  `$table->text('summary')->nullable();`
  `$table->json('misconceptions')->nullable();`
  `$table->json('recommendations')->nullable();`
- Model `TeacherAiReport`: `$casts = ['misconceptions' => 'array', 'recommendations' => 'array'];`
- In `StudyDeck`: add `hasOne(TeacherAiReport::class)`.

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Feature/StudyDeckStatusMigrationTest.php`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add config/services.php .env.example database/migrations/ app/Models/ tests/Feature/StudyDeckStatusMigrationTest.php
git commit -m "feat: add deck status, teacher ai report schema and models"
```

---

### Task 2: Core Gemini Service with Structured Schema & Simulation Fallback

**Files:**
- Create: `app/Services/GeminiService.php`
- Test: `tests/Unit/GeminiServiceTest.php`

**Interfaces:**
- Produces: `GeminiService::isConfigured(): bool`, `GeminiService::generateStructured(string $prompt, array $schema, ?string $systemInstruction = null): array`, `GeminiService::generateText(string $prompt, ?string $systemInstruction = null): string`.

- [ ] **Step 1: Write the failing test**

```php
namespace Tests\Unit;

use App\Services\GeminiService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiServiceTest extends TestCase
{
    public function test_generate_text_returns_ai_response_when_api_key_configured(): void
    {
        config(['services.gemini.key' => 'mock-gemini-key']);

        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Petunjuk: Perhatikan perkaitan cahaya.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(GeminiService::class);
        $result = $service->generateText('Beri petunjuk untuk soalan Fotosintesis.');

        $this->assertEquals('Petunjuk: Perhatikan perkaitan cahaya.', $result);
    }

    public function test_generate_structured_parses_json_schema(): void
    {
        config(['services.gemini.key' => 'mock-gemini-key']);

        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => json_encode(['topics' => [['name' => 'Klorofil', 'summary' => 'Pigmen hijau']]])],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(GeminiService::class);
        $result = $service->generateStructured('Senaraikan topik', ['type' => 'OBJECT']);

        $this->assertArrayHasKey('topics', $result);
        $this->assertEquals('Klorofil', $result['topics'][0]['name']);
    }

    public function test_falls_back_cleanly_when_api_key_empty(): void
    {
        config(['services.gemini.key' => '']);

        $service = app(GeminiService::class);
        $this->assertFalse($service->isConfigured());

        $text = $service->generateText('Ujian');
        $this->assertNotEmpty($text);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Unit/GeminiServiceTest.php`
Expected: FAIL with `Class App\Services\GeminiService not found`.

- [ ] **Step 3: Implement `GeminiService`**

- Read `config('services.gemini.key')`, `config('services.gemini.model')`, `config('services.gemini.timeout', 30)`.
- Implement `generateText` and `generateStructured` via `Http::withHeaders()->post(...)`.
- When `response_schema` provided, pass `generationConfig.responseMimeType = 'application/json'` and `generationConfig.responseSchema`.
- Clean strip any markdown code fences (````json ... ````) before json_decode.
- Implement simulation fallbacks when `isConfigured() === false` or API errors out.

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Unit/GeminiServiceTest.php`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Services/GeminiService.php tests/Unit/GeminiServiceTest.php
git commit -m "feat: implement GeminiService with structured JSON mode and fallback"
```

---

### Task 3: Asynchronous Deck Generation (`GenerateStudyDeckJob`, Controller & Polling)

**Files:**
- Create: `app/Jobs/GenerateStudyDeckJob.php`
- Modify: `app/Services/StudyDeckGeneratorService.php:80-175`
- Modify: `app/Http/Controllers/StudyDeckController.php:30-76`
- Modify: `routes/web.php:13-26`
- Create: `resources/views/decks/processing.blade.php`
- Test: `tests/Feature/AsyncDeckGenerationTest.php`

**Interfaces:**
- Consumes: `GeminiService`, `StudyDeck::$status`, `StudyDeck::$progress`.
- Produces: `GenerateStudyDeckJob`, `GET /decks/{deck}/processing`, `GET /decks/{deck}/status`.

- [ ] **Step 1: Write the failing test**

```php
namespace Tests\Feature;

use App\Jobs\GenerateStudyDeckJob;
use App\Models\StudyDeck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AsyncDeckGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploading_text_dispatches_generate_study_deck_job_and_redirects_to_processing(): void
    {
        Queue::fake();

        $response = $this->post(route('decks.store'), [
            'title' => 'Nota Sains Tingkatan 4',
            'note_text' => str_repeat('Fotosintesis merupakan proses biokimia penting tumbuhan. ', 5),
        ]);

        $deck = StudyDeck::first();
        $this->assertNotNull($deck);
        $this->assertEquals('pending', $deck->status);

        Queue::assertPushed(GenerateStudyDeckJob::class, function ($job) use ($deck) {
            return $job->deck->id === $deck->id;
        });

        $response->assertRedirect(route('decks.processing', $deck));
    }

    public function test_deck_status_endpoint_returns_json_progress(): void
    {
        $deck = StudyDeck::create([
            'title' => 'Sample Deck',
            'source_text' => 'Content here',
            'status' => 'processing',
            'progress' => 50,
        ]);

        $response = $this->getJson(route('decks.status', $deck));

        $response->assertOk()
            ->assertJson([
                'status' => 'processing',
                'progress' => 50,
            ]);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Feature/AsyncDeckGenerationTest.php`
Expected: FAIL with missing route `decks.processing` or missing job `GenerateStudyDeckJob`.

- [ ] **Step 3: Implement Async Deck Generation**

- Create `GenerateStudyDeckJob` (implements `ShouldQueue`). It:
  - Updates `$deck->update(['status' => 'processing', 'progress' => 25]);`
  - Calls `StudyDeckGeneratorService::generateDeckContent($deck)`.
  - Prompts Gemini for 3-5 topics, each with 4 MCQs (options, correct_index, explanation, quote) and 4 flashcards.
  - Inserts topics, questions, flashcards in DB transaction.
  - Updates `$deck->update(['status' => 'completed', 'progress' => 100]);`
  - Catches `Throwable`, logs to `AgentLog`, marks `$deck->update(['status' => 'failed', 'error_message' => $e->getMessage()]);`
- Update `StudyDeckController@store`:
  - If `use_sample`, retains instant sample creation.
  - Otherwise creates deck with `status: 'pending', progress: 5`, dispatches `GenerateStudyDeckJob`, redirects to `decks.processing`.
- Add routes in `routes/web.php`:
  - `Route::get('/{deck}/processing', [StudyDeckController::class, 'processing'])->name('processing');`
  - `Route::get('/{deck}/status', [StudyDeckController::class, 'status'])->name('status');`
- Create `resources/views/decks/processing.blade.php`:
  - Clean Tailwind UI with spinner, step description, progress bar, polling `/decks/{deck}/status` every 2s, redirecting to `decks.show` on completion.

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Feature/AsyncDeckGenerationTest.php`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Jobs/ app/Services/StudyDeckGeneratorService.php app/Http/Controllers/StudyDeckController.php routes/web.php resources/views/decks/ tests/Feature/AsyncDeckGenerationTest.php
git commit -m "feat: implement asynchronous deck generation job and polling UI"
```

---

### Task 4: AI Adaptive Tutor Enhancements (`AdaptiveAgentService`)

**Files:**
- Modify: `app/Services/AdaptiveAgentService.php:55-145`
- Test: `tests/Unit/AdaptiveAgentServiceAiTest.php`

**Interfaces:**
- Consumes: `GeminiService`, `StudyDeck`, `Topic`.
- Produces: `AdaptiveAgentService::determineNextMission(StudyDeck $deck): array` (with cached Gemini pedagogical rationale).

- [ ] **Step 1: Write the failing test**

```php
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
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Unit/AdaptiveAgentServiceAiTest.php`
Expected: FAIL with constructor mismatch or missing Gemini enrichment.

- [ ] **Step 3: Implement Gemini-enhanced Adaptive Agent**

- Inject `GeminiService` into `AdaptiveAgentService`.
- In `determineNextMission`: Keep core algorithm selecting activity based on accuracy and HP.
- Before returning, if `$this->gemini->isConfigured()`, generate or fetch from `Cache::remember("deck_{$deck->id}_adaptive_rationale_{$topicId}_{$activity}", 300, ...)` a targeted 1-2 sentence coaching advice in Malay addressing learner mistakes.
- If timeout, exception, or unconfigured, fallback immediately to existing static rationale.

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Unit/AdaptiveAgentServiceAiTest.php`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Services/AdaptiveAgentService.php tests/Unit/AdaptiveAgentServiceAiTest.php
git commit -m "feat: enrich adaptive agent with cached Gemini pedagogical rationales"
```

---

### Task 5: Interactive In-Activity AI Scaffolding (Hints & Explanations)

**Files:**
- Modify: `app/Http/Controllers/StudyActivityController.php:50-130`
- Modify: `routes/web.php:20-26`
- Modify: `resources/views/activities/partials/activity-header.blade.php:1-40`
- Modify: `resources/views/activities/partials/activity-complete.blade.php:1-60`
- Test: `tests/Feature/StudyActivityAiScaffoldingTest.php`

**Interfaces:**
- Consumes: `GeminiService`, `Question`.
- Produces: `POST /decks/{deck}/ai-hint`, `POST /decks/{deck}/ai-explain`.

- [ ] **Step 1: Write the failing test**

```php
namespace Tests\Feature;

use App\Models\Question;
use App\Models\StudyDeck;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyActivityAiScaffoldingTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_hint_returns_contextual_hint_without_revealing_answer(): void
    {
        $deck = StudyDeck::create(['title' => 'Fizik', 'source_text' => 'Hukum Newton...', 'status' => 'completed']);
        $topic = $deck->topics()->create(['name' => 'Daya', 'summary' => 'Hukum Gerakan']);
        $question = $topic->questions()->create([
            'study_deck_id' => $deck->id,
            'question' => 'Apakah unit bagi daya?',
            'options' => ['Newton', 'Joule', 'Watt', 'Pascal'],
            'correct_index' => 0,
            'difficulty' => 1,
            'explanation' => 'Daya diukur dalam Newton.',
            'quote' => 'Unit SI bagi daya ialah Newton (N).',
        ]);

        $this->mock(GeminiService::class, function ($mock) {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('generateText')->andReturn('Fikirkan tentang saintis yang menemui graviti.');
        });

        $response = $this->postJson(route('decks.ai-hint', $deck), [
            'question_id' => $question->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'hint' => 'Fikirkan tentang saintis yang menemui graviti.',
            ]);
    }

    public function test_ai_explain_provides_targeted_feedback_for_mistake(): void
    {
        $deck = StudyDeck::create(['title' => 'Fizik', 'source_text' => 'Hukum Newton...', 'status' => 'completed']);
        $topic = $deck->topics()->create(['name' => 'Daya', 'summary' => 'Hukum Gerakan']);
        $question = $topic->questions()->create([
            'study_deck_id' => $deck->id,
            'question' => 'Apakah unit bagi daya?',
            'options' => ['Newton', 'Joule', 'Watt', 'Pascal'],
            'correct_index' => 0,
            'difficulty' => 1,
            'explanation' => 'Daya diukur dalam Newton.',
            'quote' => 'Unit SI bagi daya ialah Newton (N).',
        ]);

        $this->mock(GeminiService::class, function ($mock) {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('generateText')->andReturn('Joule ialah unit bagi tenaga, bukan daya.');
        });

        $response = $this->postJson(route('decks.ai-explain', $deck), [
            'question_id' => $question->id,
            'selected_index' => 1,
        ]);

        $response->assertOk()
            ->assertJson([
                'explanation' => 'Joule ialah unit bagi tenaga, bukan daya.',
            ]);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Feature/StudyActivityAiScaffoldingTest.php`
Expected: FAIL with routes not found (`decks.ai-hint`, `decks.ai-explain`).

- [ ] **Step 3: Implement AI Hint and Explanation Endpoints & UI**

- In `StudyActivityController`:
  - `aiHint(StudyDeck $deck, Request $request)`: Validates `question_id`, retrieves question & quote, prompts Gemini for Socratic hint in Malay, returns JSON `['hint' => $hint]`.
  - `aiExplain(StudyDeck $deck, Request $request)`: Validates `question_id, selected_index`, prompts Gemini to explain specifically why the selected choice was wrong and guide back to the concept quote, returns JSON `['explanation' => $explanation]`.
- Add routes in `routes/web.php`:
  - `Route::post('/{deck}/ai-hint', [StudyActivityController::class, 'aiHint'])->name('ai-hint');`
  - `Route::post('/{deck}/ai-explain', [StudyActivityController::class, 'aiExplain'])->name('ai-explain');`
- Add "Bantuan Petunjuk AI" button in `activity-header.blade.php`.
- Add "Terangkan Lagi dengan AI" button in wrong-answer feedback modal.

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Feature/StudyActivityAiScaffoldingTest.php`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/StudyActivityController.php routes/web.php resources/views/activities/ tests/Feature/StudyActivityAiScaffoldingTest.php
git commit -m "feat: add interactive in-quiz AI hints and post-attempt explanation endpoints"
```

---

### Task 6: Asynchronous Teacher Diagnostic Report (`GenerateTeacherReportJob` & UI)

**Files:**
- Create: `app/Jobs/GenerateTeacherReportJob.php`
- Modify: `app/Http/Controllers/TeacherReportController.php:1-35`
- Modify: `routes/web.php:18-20`
- Modify: `resources/views/decks/report.blade.php:1-120`
- Test: `tests/Feature/TeacherAiReportGenerationTest.php`

**Interfaces:**
- Consumes: `TeacherAiReport`, `GeminiService`, `StudySession`, `AgentLog`.
- Produces: `POST /decks/{deck}/report/generate`, `GET /decks/{deck}/report/status`.

- [ ] **Step 1: Write the failing test**

```php
namespace Tests\Feature;

use App\Jobs\GenerateTeacherReportJob;
use App\Models\StudyDeck;
use App\Models\TeacherAiReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TeacherAiReportGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_generate_teacher_ai_report_without_sessions(): void
    {
        $deck = StudyDeck::create(['title' => 'Sains', 'source_text' => 'Nota...', 'status' => 'completed']);

        $response = $this->postJson(route('decks.report.generate', $deck));

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Laporan diagnostik AI memerlukan sekurang-kurangnya satu sesi aktiviti pelajar.',
            ]);
    }

    public function test_dispatches_generate_teacher_report_job_when_sessions_exist(): void
    {
        Queue::fake();

        $deck = StudyDeck::create(['title' => 'Sains', 'source_text' => 'Nota...', 'status' => 'completed']);
        $deck->studySessions()->create([
            'activity_type' => 'blitz',
            'score' => 80,
            'right_count' => 8,
            'wrong_count' => 2,
            'best_combo' => 4,
        ]);

        $response = $this->postJson(route('decks.report.generate', $deck));

        $response->assertOk()
            ->assertJson(['status' => 'pending']);

        Queue::assertPushed(GenerateTeacherReportJob::class);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Feature/TeacherAiReportGenerationTest.php`
Expected: FAIL with route `decks.report.generate` not defined.

- [ ] **Step 3: Implement Teacher AI Report Job, Controller & View**

- Create `GenerateTeacherReportJob`:
  - Receives `StudyDeck $deck`.
  - Queries topics, session stats, questions with highest error rates, mistake list.
  - Calls `GeminiService::generateStructured(...)` for schema:
    `{ summary: string, misconceptions: string[], recommendations: string[] }`.
  - Updates `TeacherAiReport` with `status: 'completed', summary, misconceptions, recommendations`.
- In `TeacherReportController`:
  - `generate(StudyDeck $deck)`: Gated by `deck->studySessions()->count() > 0`. Dispatches job protected by `Cache::lock("teacher_report_{$deck->id}", 60)`.
  - `status(StudyDeck $deck)`: Returns JSON report status and data for polling.
  - `show(StudyDeck $deck)`: Passes `aiReport` to `decks.report` view.
- Update `resources/views/decks/report.blade.php`:
  - Add "Analisis Diagnostik Pintar (AI Teacher Insights)" card with status badge, executive summary, key student misconceptions list, and remedial action items.
  - If report is missing or pending, render "Jana Analisis AI" button with live polling state.

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Feature/TeacherAiReportGenerationTest.php`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Jobs/GenerateTeacherReportJob.php app/Http/Controllers/TeacherReportController.php routes/web.php resources/views/decks/report.blade.php tests/Feature/TeacherAiReportGenerationTest.php
git commit -m "feat: implement async AI teacher diagnostic reports with polling UI"
```

---

### Task 7: Full Test Suite Verification, Code Formatting & End-to-End Walkthrough

**Files:**
- Modify: All affected files
- Run: Pint formatter, full test runner

- [ ] **Step 1: Run Pint code formatter**

Run: `vendor/bin/pint --format agent`
Expected: All files formatted cleanly to Laravel standards.

- [ ] **Step 2: Run complete test suite**

Run: `php artisan test`
Expected: All tests PASS with 0 failures, 0 errors.

- [ ] **Step 3: Commit formatting and finalized changes**

```bash
git add .
git commit -m "chore: format codebase with pint and verify all tests pass"
```
