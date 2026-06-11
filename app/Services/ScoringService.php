<?php

namespace App\Services;

use App\Models\ExamSession;
use App\Models\User;

class ScoringService
{
    /**
     * Get grade based on percentage
     * 90-100%: A+
     * 80-89%: A
     * 70-79%: B
     * 60-69%: C
     * 50-59%: D
     * Below 50%: F
     */
    public function getGrade($percentage)
    {
        if ($percentage >= 90) {
            return [
                'grade' => 'A+',
                'color' => '#28a745',
                'remarks' => 'Outstanding! Exceptional understanding.',
                'emoji' => '🌟'
            ];
        } elseif ($percentage >= 80) {
            return [
                'grade' => 'A',
                'color' => '#28a745',
                'remarks' => 'Excellent! Very good understanding.',
                'emoji' => '🎉'
            ];
        } elseif ($percentage >= 70) {
            return [
                'grade' => 'B',
                'color' => '#17a2b8',
                'remarks' => 'Good! Solid understanding.',
                'emoji' => '✅'
            ];
        } elseif ($percentage >= 60) {
            return [
                'grade' => 'C',
                'color' => '#ffc107',
                'remarks' => 'Satisfactory! Keep practicing.',
                'emoji' => '👍'
            ];
        } elseif ($percentage >= 50) {
            return [
                'grade' => 'D',
                'color' => '#fd7e14',
                'remarks' => 'Needs Improvement. Focus on weak areas.',
                'emoji' => '📚'
            ];
        } else {
            return [
                'grade' => 'F',
                'color' => '#dc3545',
                'remarks' => 'Failed. Requires significant improvement.',
                'emoji' => '⚠️'
            ];
        }
    }

    /**
     * Get score breakdown by difficulty
     */
    public function getScoreByDifficulty(ExamSession $session)
    {
        $answerLogs = $session->answerLogs()->with('mcq')->get();

        $easy = $answerLogs->filter(fn($log) => $log->mcq->difficulty === 'easy');
        $medium = $answerLogs->filter(fn($log) => $log->mcq->difficulty === 'medium');
        $hard = $answerLogs->filter(fn($log) => $log->mcq->difficulty === 'hard');

        return [
            'easy' => [
                'total' => $easy->count(),
                'correct' => $easy->where('is_correct', true)->count(),
                'percentage' => $easy->count() > 0 ? round(($easy->where('is_correct', true)->count() / $easy->count()) * 100) : 0,
            ],
            'medium' => [
                'total' => $medium->count(),
                'correct' => $medium->where('is_correct', true)->count(),
                'percentage' => $medium->count() > 0 ? round(($medium->where('is_correct', true)->count() / $medium->count()) * 100) : 0,
            ],
            'hard' => [
                'total' => $hard->count(),
                'correct' => $hard->where('is_correct', true)->count(),
                'percentage' => $hard->count() > 0 ? round(($hard->where('is_correct', true)->count() / $hard->count()) * 100) : 0,
            ],
        ];
    }

    /**
     * Get user's all-time analytics
     */
    public function getUserAnalytics(User $user)
    {
        $sessions = ExamSession::where('user_id', $user->id)
            ->where(function ($query) {
                $query->where('status', 'completed')
                      ->orWhere('status', 'expired');
            })
            ->with('subject')
            ->get();

        $totalExams = $sessions->count();
        $averageScore = $totalExams > 0 ? round($sessions->avg('percentage'), 2) : 0;
        $highestScore = $totalExams > 0 ? round($sessions->max('percentage'), 2) : 0;
        $lowestScore = $totalExams > 0 ? round($sessions->min('percentage'), 2) : 0;

        // Subject-wise performance
        $subjectPerformance = $sessions->groupBy('subject_id')->map(function ($subjectSessions) {
            return [
                'total' => $subjectSessions->count(),
                'average' => round($subjectSessions->avg('percentage'), 2),
                'highest' => round($subjectSessions->max('percentage'), 2),
                'lowest' => round($subjectSessions->min('percentage'), 2),
            ];
        });

        return [
            'total_exams' => $totalExams,
            'average_score' => $averageScore,
            'highest_score' => $highestScore,
            'lowest_score' => $lowestScore,
            'pass_count' => $sessions->where('percentage', '>=', 50)->count(),
            'fail_count' => $sessions->where('percentage', '<', 50)->count(),
            'subject_performance' => $subjectPerformance,
            'all_sessions' => $sessions,
        ];
    }

    /**
     * Get performance trend data
     */
    public function getPerformanceTrend(User $user, $limit = 10)
    {
        return ExamSession::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'expired'])
            ->with('subject')
            ->orderBy('finished_at', 'desc')
            ->limit($limit)
            ->get(['id', 'subject_id', 'percentage', 'finished_at', 'correct_answers', 'total_questions'])
            ->reverse()
            ->values();
    }
}