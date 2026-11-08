<?php

declare(strict_types=1);

use App\Http\Controllers\StudyActivityController;
use App\Http\Controllers\StudyDeckController;
use App\Http\Controllers\TeacherReportController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/cara-ia-berfungsi', 'how-it-works')->name('how-it-works');

// Study Deck Routes
Route::prefix('decks')->name('decks.')->group(function (): void {
    Route::get('/create', [StudyDeckController::class, 'create'])->name('create');
    Route::post('/', [StudyDeckController::class, 'store'])->name('store')->middleware('throttle:15,1');
    Route::get('/{deck}/processing', [StudyDeckController::class, 'processing'])->name('processing');
    Route::get('/{deck}/status', [StudyDeckController::class, 'status'])->name('status');
    Route::get('/{deck}', [StudyDeckController::class, 'show'])->name('show');
    Route::get('/{deck}/report', [TeacherReportController::class, 'show'])->name('report');

    // Interactive Study Activity Endpoints
    Route::get('/{deck}/activities/{activity}', [StudyActivityController::class, 'show'])->name('activities.show');
    Route::post('/{deck}/verify-answer', [StudyActivityController::class, 'verifyAnswer'])->name('verify-answer');
    Route::post('/{deck}/finish-activity', [StudyActivityController::class, 'finishActivity'])->name('finish-activity');
    Route::post('/{deck}/boss-loss', [StudyActivityController::class, 'handleBossLoss'])->name('boss-loss');
});
