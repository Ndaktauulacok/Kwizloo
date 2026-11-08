# Gemini AI Integration Architecture Design

## Intent and Why
KwizLoo currently uses deterministic heuristics and hardcoded string splitting for deck creation, rule-based heuristics for adaptive tutoring, and static aggregation for teacher reports.
This project integrates Google Gemini (`gemini-2.5-flash`) across every core functional pillar:
1. Grounded study deck generation from source notes/documents.
2. Async background processing with queue workers, database status tracking, and polling UI.
3. Adaptive cognitive tutor recommendations driven by learner error patterns.
4. Interactive in-activity scaffolding (contextual hints and Socratic explanations).
5. Automated diagnostic teacher reports highlighting knowledge gaps and pedagogical interventions.

---

## Goals, Non-Goals, and Anti-Goals

### Goals
- **Gemini Service Layer (`App\Services\GeminiService`)**: Clean HTTP client wrapper around Google Gemini REST API supporting structured JSON responses (`response_mime_type: "application/json"`), system instructions, configurable temperature, and automated fallback to simulation when `GEMINI_API_KEY` is absent or unconfigured.
- **Asynchronous Deck Generation**: Note and document processing dispatched via `GenerateStudyDeckJob` to Laravel's queue. Real-time status tracked via database (`status`: `pending`, `processing`, `completed`, `failed`; `progress`: 0-100%).
- **Polling Loading Interface**: Modern, responsive processing view (`decks/processing.blade.php`) polling `GET /decks/{deck}/status` every 2 seconds, displaying dynamic progress messages and auto-redirecting to the deck upon completion.
- **Grounded Topical Generation**: AI deck generator extracts 3-5 coherent topics, each with 4-5 verified multiple-choice questions (4 options, clear explanation, direct textual quote) and 4-5 flashcards grounded in the source text.
- **Adaptive Agent AI**: `AdaptiveAgentService` incorporates Gemini to evaluate learner accuracy, mistake history, and cognitive fatigue to generate contextual pedagogical rationales for the recommended next mission.
- **Interactive Scaffolding**: Endpoints for in-quiz AI hints and post-attempt answer explanations that guide students without revealing direct answers immediately.
- **Teacher Diagnostic AI**: `GenerateTeacherReportJob` producing executive diagnostic summaries, detected misconceptions, and targeted classroom recommendations.

### Non-Goals
- Real-time voice/audio generation or speech-to-text.
- WebSocket infrastructure (Pusher, Soketi, or Laravel Reverb) — polling was chosen for zero external operational dependencies.
- Replacing existing sample pack flow: "Use Sample" button continues to provide instant verified photosynthesis deck.

### Anti-Goals
- Hallucinatory content: All questions, answers, and flashcards must be grounded in source text with direct quotation references.
- Blocking UI threads: LLM deck generation and report generation must never execute synchronously within web request cycles.
- Breaking offline development: System must cleanly fall back to deterministic mocks if no API key is supplied.

---

## Constraints
- **PHP Version**: 8.3+ (project runs on PHP 8.5).
- **Framework**: Laravel 13.x with Eloquent ORM.
- **Database**: MySQL (`kwizloo` / `kwizloo_test`).
- **Queue Connection**: Database queue driver (`queue:work`) or `sync` for testing/fallback.
- **API Provider**: Google Gemini REST API (`https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent`).

---

## Approaches Considered & Selected

### Approach 1: Database Status with Polling (Selected)
- Background jobs record `status`, `progress`, and `error_message` in the database.
- Controller redirects user to `/decks/{deck}/processing`.
- Alpine.js / Vanilla JS polls `GET /decks/{deck}/status` every 2 seconds until `completed` or `failed`.
- *Why chosen*: Native Laravel database queue, zero extra daemons, resilient against network interruptions, completely portable.

### Approach 2: Server-Sent Events (SSE) (Rejected)
- Long-running HTTP connection streaming progress events.
- *Why rejected*: Sensitive to web server output buffering, proxy timeouts, and PHP worker starvation on standard servers.

### Approach 3: WebSockets with Laravel Reverb (Rejected)
- Event-driven push notifications over WebSocket connections.
- *Why rejected*: Requires dedicated daemon processes, port configuration, and extra client-side build setup for a brief 5-15s wait.

---

## System Architecture & Implementation Details

### 1. Configuration & Environment
- `.env` and `.env.example`:
  ```env
  GEMINI_API_KEY=
  GEMINI_MODEL=gemini-2.5-flash
  ```
- `config/services.php`:
  ```php
  'gemini' => [
      'key' => env('GEMINI_API_KEY'),
      'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
      'timeout' => env('GEMINI_TIMEOUT', 30),
  ],
  ```

### 2. Database Migrations
- Modify `study_decks` table:
  - `status` (`varchar(30)`, default `'completed'`) — values: `'pending'`, `'processing'`, `'completed'`, `'failed'`.
  - `progress` (`unsignedTinyInteger`, default `100`).
  - `error_message` (`text`, nullable).
- Create `teacher_ai_reports` table:
  - `id` (bigint auto-increment)
  - `study_deck_id` (foreign key to `study_decks`, cascade on delete)
  - `status` (`varchar(30)`, default `'pending'`)
  - `summary` (`text`, nullable)
  - `misconceptions` (`json`, nullable)
  - `recommendations` (`json`, nullable)
  - `created_at`, `updated_at`

### 3. Core Service: `GeminiService`
- Located at `app/Services/GeminiService.php`.
- Key methods:
  - `generateStructured(string $prompt, array $schema, ?string $systemInstruction = null): array`: Invokes Gemini API with `response_mime_type: "application/json"` and `response_schema`. Parses and validates returned JSON.
  - `generateText(string $prompt, ?string $systemInstruction = null): string`: Standard text generation for hints, rationales, and explanations.
  - `isConfigured(): bool`: Returns true if `GEMINI_API_KEY` is present.
  - Graceful fallback: If not configured, delegates to deterministic fallback generator or returns simulated scaffolding responses.

### 4. Background Jobs
- `App\Jobs\GenerateStudyDeckJob`:
  - Receives `StudyDeck $deck, string $sourceText`.
  - Updates `$deck->update(['status' => 'processing', 'progress' => 25])`.
  - Calls `StudyDeckGeneratorService->generateFromTextWithGemini(...)`.
  - Updates progress through steps (parsing: 25%, topics & questions: 70%, persisting & logging: 95%).
  - On success: marks `status = 'completed'`, `progress = 100`.
  - On catchable failure: logs `AgentLog` with error, updates `status = 'failed'`, `error_message = $e->getMessage()`.
- `App\Jobs\GenerateTeacherReportJob`:
  - Receives `StudyDeck $deck`.
  - Aggregates deck questions, student sessions, accuracy, and mistake logs.
  - Queries Gemini to synthesize high-level diagnostic report.
  - Updates or creates `TeacherAiReport` record.

### 5. Controller Endpoints & Routes
- `StudyDeckController`:
  - `store`: When not sample deck, creates `StudyDeck` with `status = 'pending'`, dispatches `GenerateStudyDeckJob`, and returns redirect to `route('decks.processing', $deck)`.
  - `processing(StudyDeck $deck)`: Returns `view('decks.processing', compact('deck'))`.
  - `status(StudyDeck $deck)`: Returns JSON `['status' => $deck->status, 'progress' => $deck->progress, 'redirect_url' => route('decks.show', $deck), 'error' => $deck->error_message]`.
- `StudyActivityController`:
  - `POST /decks/{deck}/ai-hint`: Accepts `question_id`. Returns JSON `{ hint: string }` generated by Gemini based on question quote and question text, steering the student toward the answer without revealing it.
  - `POST /decks/{deck}/ai-explain`: On-demand only (Builder Call). Invoked when student clicks "Terangkan Lagi dengan AI" button upon an incorrect answer. Keeps fast-paced activities snappy.
- `TeacherReportController`:
  - `POST /decks/{deck}/report/generate`: Dispatches `GenerateTeacherReportJob` guarded by atomic cache lock (`teacher_report_deck_{$id}`) to prevent duplicate jobs (Builder Call). Gated to decks with at least 1 recorded session (Builder Call).
  - `GET /decks/{deck}/report/status`: Returns JSON `{ status, report, error }` for polling the report state on the report page.

### 6. Adaptive Agent Performance Model (Builder Call)
- `AdaptiveAgentService::determineNextMission()`:
  - Fast rule-based activity selection runs immediately.
  - Contextual AI rationale generated via Gemini is cached in `Cache` for 300 seconds keyed by `deck_{$deck->id}_adaptive_rationale_{$topicId}_{$activity}`.
  - If Gemini call exceeds 2 seconds or fails, immediately falls back to deterministic rule rationale. Zero page-load hang.

### 7. User Interface Updates
- `resources/views/decks/processing.blade.php`:
  - Clean card centered on page with animated pulse icon, dynamic step description, progress bar, and automatic redirect script.
- `resources/views/decks/show.blade.php`:
  - Displays AI-generated adaptive rationale badge under Next Recommended Mission.
- `resources/views/activities/partials/activity-header.blade.php` & activity views:
  - "Minta Petunjuk AI" (Request AI Hint) button opening an unobtrusive helper drawer or popover.
  - "Terangkan Pilihan Saya" button on incorrect answer modal.
- `resources/views/decks/report.blade.php`:
  - "Analisis Diagnostik Pintar (AI Teacher Insights)" card with executive overview, identified student blind spots, and proposed remedial classroom activities. Includes "Jana Analisis AI" button with live polling state if report is pending.

---

## What is Left to the Builder
- Precise prompt engineering for Gemini to ensure responses conform to Bahasa Melayu and/or English matching the source input.
- Exact styling details of progress bar, hint modal, and report cards using existing Tailwind design tokens.
- Unit and Feature tests mocking external HTTP calls via `Http::fake()`.
