<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\GenerateTeacherReportJob;
use App\Models\StudyDeck;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TeacherReportController extends Controller
{
    public function show(StudyDeck $deck): View
    {
        $deck->load(['topics.questions', 'studySessions', 'agentLogs', 'teacherAiReport']);

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
            'aiReport' => $deck->teacherAiReport,
        ]);
    }

    public function generate(StudyDeck $deck): JsonResponse
    {
        if ($deck->studySessions()->count() === 0) {
            return response()->json([
                'message' => 'Laporan diagnostik AI memerlukan sekurang-kurangnya satu sesi aktiviti pelajar.',
            ], 422);
        }

        Cache::lock("teacher_report_{$deck->id}", 60)->get(function () use ($deck): void {
            GenerateTeacherReportJob::dispatch($deck);
        });

        return response()->json([
            'status' => 'pending',
        ]);
    }

    public function status(StudyDeck $deck): JsonResponse
    {
        $report = $deck->teacherAiReport;

        return response()->json([
            'status' => $report ? $report->status : 'none',
            'summary' => $report ? $report->summary : null,
            'misconceptions' => $report ? ($report->misconceptions ?? []) : [],
            'recommendations' => $report ? ($report->recommendations ?? []) : [],
        ]);
    }
}
