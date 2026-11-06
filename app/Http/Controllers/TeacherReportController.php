<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\StudyDeck;
use Illuminate\View\View;

class TeacherReportController extends Controller
{
    public function show(StudyDeck $deck): View
    {
        $deck->load(['topics.questions', 'studySessions', 'agentLogs']);

        $totalSessions = $deck->studySessions->count();
        $totalQuestionsAnswered = $deck->studySessions->sum('right_count') + $deck->studySessions->sum('wrong_count');
        $overallAccuracy = $totalQuestionsAnswered > 0
            ? (int) round(($deck->studySessions->sum('right_count') / $totalQuestionsAnswered) * 100)
            : 0;

        return view('decks.report', [
            'deck' => $deck,
            'topics' => $deck->topics,
            'totalSessions' => $totalSessions,
            'overallAccuracy' => $overallAccuracy,
            'logs' => $deck->agentLogs,
            'recentSessions' => $deck->studySessions()->latest()->take(10)->get(),
        ]);
    }
}
