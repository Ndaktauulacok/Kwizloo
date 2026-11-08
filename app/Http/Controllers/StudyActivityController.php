<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\StudyDeck;
use App\Models\StudySession;
use App\Models\Topic;
use App\Services\AdaptiveAgentService;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyActivityController extends Controller
{
    private readonly GeminiService $gemini;

    public function __construct(
        private readonly AdaptiveAgentService $adaptiveAgent,
        ?GeminiService $gemini = null,
    ) {
        $this->gemini = $gemini ?? app(GeminiService::class);
    }

    public function show(StudyDeck $deck, string $activity, Request $request): View
    {
        if (! array_key_exists($activity, AdaptiveAgentService::ACTIVITIES)) {
            abort(404, 'Mod aktiviti pembelajaran tidak ditemui.');
        }

        $topicId = $request->integer('topic_id');
        $topic = $topicId > 0 ? $deck->topics()->find($topicId) : null;

        // Fetch questions for this activity
        $query = $topic ? $topic->questions() : $deck->questions();
        $questions = $query->with('topic')->inRandomOrder()->take(20)->get();

        // Fetch flashcards if cards activity
        $cardQuery = $topic ? $topic->flashcards() : $deck->flashcards();
        $flashcards = $cardQuery->with('topic')->inRandomOrder()->take(25)->get();

        return view('activities.'.$activity, [
            'deck' => $deck,
            'topic' => $topic,
            'activity' => $activity,
            'activityMeta' => AdaptiveAgentService::ACTIVITIES[$activity],
            'questions' => $questions,
            'flashcards' => $flashcards,
        ]);
    }

    /**
     * Server-side grading endpoint. Prevents client score spoofing.
     */
    public function verifyAnswer(StudyDeck $deck, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
            'selected_index' => ['required', 'integer', 'min:0', 'max:3'],
            'is_boss' => ['nullable', 'boolean'],
        ]);

        /** @var Question $question */
        $question = $deck->questions()->with('topic')->findOrFail($validated['question_id']);
        $topic = $question->topic;

        $isCorrect = $question->isCorrect($validated['selected_index']);

        // Record attempt in adaptive engine
        $this->adaptiveAgent->recordAttempt($topic, $isCorrect, $isCorrect ? null : $question->question);

        $freshTopic = $topic->fresh();

        return response()->json([
            'is_correct' => $isCorrect,
            'correct_index' => $question->correct_index,
            'explanation' => $question->explanation,
            'quote' => $question->quote,
            'topic' => [
                'id' => $freshTopic->id,
                'name' => $freshTopic->name,
                'hp' => $freshTopic->hp,
                'max_hp' => $freshTopic->max_hp,
                'status' => $freshTopic->status,
                'accuracy' => $freshTopic->accuracy_percentage,
            ],
        ]);
    }

    /**
     * Save completed study session and return next recommendation.
     */
    public function finishActivity(StudyDeck $deck, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'activity_type' => ['required', 'string'],
            'topic_id' => ['nullable', 'integer', 'exists:topics,id'],
            'score' => ['required', 'integer', 'min:0'],
            'right_count' => ['required', 'integer', 'min:0'],
            'wrong_count' => ['required', 'integer', 'min:0'],
            'best_combo' => ['required', 'integer', 'min:0'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
        ]);

        $session = StudySession::create([
            'study_deck_id' => $deck->id,
            'topic_id' => $validated['topic_id'] ?? null,
            'activity_type' => $validated['activity_type'],
            'score' => $validated['score'],
            'right_count' => $validated['right_count'],
            'wrong_count' => $validated['wrong_count'],
            'best_combo' => $validated['best_combo'],
            'duration_seconds' => $validated['duration_seconds'] ?? 0,
        ]);

        $nextMission = $this->adaptiveAgent->determineNextMission($deck);

        return response()->json([
            'session_id' => $session->id,
            'next_mission' => $nextMission,
        ]);
    }

    /**
     * Handle boss defeat of student: lowers difficulty and schedules spaced rematch.
     */
    public function handleBossLoss(StudyDeck $deck, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'topic_id' => ['required', 'integer', 'exists:topics,id'],
        ]);

        /** @var Topic $topic */
        $topic = $deck->topics()->findOrFail($validated['topic_id']);
        $newStrategy = $this->adaptiveAgent->adjustStrategyAfterLoss($topic);

        return response()->json([
            'strategy' => $newStrategy,
            'topic' => $topic->fresh(),
        ]);
    }

    /**
     * Return contextual AI hint for a question.
     */
    public function aiHint(StudyDeck $deck, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
        ]);

        /** @var Question $question */
        $question = $deck->questions()->findOrFail($validated['question_id']);

        $prompt = "Berikan satu petunjuk Sokratik ringkas (maksimum 20 patah perkataan) dalam Bahasa Melayu untuk soalan berikut tanpa mendedahkan jawapan secara langsung.\nSoalan: {$question->question}\nPetikan Teks: {$question->quote}\nPetunjuk:";

        $hint = $this->gemini->generateText($prompt, 'Anda ialah tutor bimbingan yang bijak memberi petunjuk tanpa membocorkan jawapan.');

        return response()->json([
            'hint' => $hint,
        ]);
    }

    /**
     * Return contextual AI explanation for an incorrect student answer choice.
     */
    public function aiExplain(StudyDeck $deck, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
            'selected_index' => ['required', 'integer', 'min:0', 'max:3'],
        ]);

        /** @var Question $question */
        $question = $deck->questions()->findOrFail($validated['question_id']);
        $selectedText = $question->options[$validated['selected_index']] ?? 'Pilihan murid';
        $correctText = $question->options[$question->correct_index] ?? 'Jawapan betul';

        $prompt = "Pelajar memilih jawapan yang salah dalam kuiz.\nSoalan: {$question->question}\nPilihan murid yang salah: {$selectedText}\nJawapan tepat sebenar: {$correctText}\nPetikan rujukan: {$question->quote}\nTerangkan secara ringkas dan membina (maksimum 30 patah perkataan) mengapa pilihan murid tidak tepat dan hubung kaitkan dengan fakta dalam teks.";

        $explanation = $this->gemini->generateText($prompt, 'Anda ialah guru yang sabar dan membina, menerangkan kesalahan konsep murid secara jelas dan menyokong.');

        return response()->json([
            'explanation' => $explanation,
        ]);
    }
}
