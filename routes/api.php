<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Exam\ExamSessionController;
use App\Http\Controllers\Exam\ResultController;
use App\Http\Controllers\Student\DashboardController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Authenticated API Routes
Route::middleware('auth:sanctum')->group(function () {
    // User Info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Exam API
    Route::prefix('exam')->group(function () {
        Route::post('/{session}/answer', [ExamSessionController::class, 'submitAnswer']);
        Route::get('/{session}/time-remaining', [ExamSessionController::class, 'getTimeRemaining']);
        Route::get('/{session}/progress', [ExamSessionController::class, 'getProgress']);
        Route::get('/{session}/result/{mcqId}', [ResultController::class, 'getAnswerDetail']);
    });

    // Dashboard API
    Route::prefix('student')->group(function () {
        Route::get('/analytics', [DashboardController::class, 'analytics']);
        Route::get('/exam-history', [DashboardController::class, 'examHistory']);
        Route::get('/leaderboard', [DashboardController::class, 'leaderboard']);
    });
});