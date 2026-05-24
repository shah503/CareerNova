<?php

namespace App\Services;

use App\Models\User;
use App\Models\ExamSession;
use App\Models\AnswerLog;
use App\Models\Leaderboard;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get student overall analytics
     * 
     * @param User $user
     * @return array
     */
    public function getStudentAnalytics(User $user)
    {
        $sessions = $user->examSessions()->where('status', 'completed')->get();

        $totalTests = $sessions->count();
        $totalMcqs = $user->answerLogs()->count();
        $totalCorrect = $user->answerLogs()->where('is_correct', true)->count();
        $averagePercentage = $sessions->avg('percentage') ?? 0;

        return [
            'total_tests' => $totalTests,
            'total_mcqs_answered' => $totalMcqs,
            'total_correct_answers' => $totalCorrect,
            'overall_percentage' => round($averagePercentage, 2),
            'success_rate' => $totalMcqs > 0 ? ($totalCorrect / $totalMcqs) * 100 : 0,
        ];
    }

    /**
     * Get subject-wise analytics for a student
     * 
     * @param User $user
     * @return array
     */
    public function getSubjectWiseAnalytics(User $user)
    {
        $sessions = $user->examSessions()
            ->where('status', 'completed')
            ->with('subject')
            ->get()
            ->groupBy('subject_id');

        $analytics = [];

        foreach ($sessions as $subjectId => $subjectSessions) {
            $subject = $subjectSessions->first()->subject;
            $totalTests = $subjectSessions->count();
            $totalMcqs = $subjectSessions->sum('total_questions');
            $totalCorrect = $subjectSessions->sum('correct_answers');
            $avgPercentage = $subjectSessions->avg('percentage');

            $analytics[] = [
                'subject_id' => $subjectId,
                'subject_name' => $subject->name,
                'total_tests' => $totalTests,
                'total_mcqs' => $totalMcqs,
                'correct_answers' => $totalCorrect,
                'average_percentage' => round($avgPercentage, 2),
                'success_rate' => $totalMcqs > 0 ? ($totalCorrect / $totalMcqs) * 100 : 0,
            ];
        }

        return $analytics;
    }

    /**
     * Get leaderboard rankings
     * 
     * @param string $type overall|weekly|monthly
     * @param int $limit
     * @return array
     */
    public function getLeaderboard($type = 'overall', $limit = 10)
    {
        $query = Leaderboard::query();

        $orderBy = match($type) {
            'weekly' => 'weekly_rank',
            'monthly' => 'monthly_rank',
            default => 'overall_rank',
        };

        $leaderboard = $query->orderBy($orderBy, 'asc')
            ->limit($limit)
            ->with('user')
            ->get()
            ->map(function ($entry, $index) use ($type) {
                return [
                    'rank' => $index + 1,
                    'user_name' => $entry->user->name,
                    'user_id' => $entry->user->id,
                    'tests' => $entry->total_tests,
                    'score' => $this->getScoreByType($entry, $type),
                    'percentage' => $entry->overall_percentage,
                ];
            });

        return $leaderboard->toArray();
    }

    /**
     * Get user rank in leaderboard
     * 
     * @param User $user
     * @param string $type
     * @return int|null
     */
    public function getUserRank(User $user, $type = 'overall')
    {
        $leaderboard = $user->leaderboard;

        if (!$leaderboard) {
            return null;
        }

        return match($type) {
            'weekly' => $leaderboard->weekly_rank,
            'monthly' => $leaderboard->monthly_rank,
            default => $leaderboard->overall_rank,
        };
    }

    /**
     * Update leaderboard for a user
     * 
     * @param User $user
     * @return void
     */
    public function updateLeaderboard(User $user)
    {
        $analytics = $this->getStudentAnalytics($user);

        Leaderboard::updateOrCreate(
            ['user_id' => $user->id],
            [
                'total_tests' => $analytics['total_tests'],
                'total_mcqs_answered' => $analytics['total_mcqs_answered'],
                'total_correct_answers' => $analytics['total_correct_answers'],
                'overall_percentage' => $analytics['overall_percentage'],
            ]
        );

        $this->calculateRanks();
    }

    /**
     * Calculate and update all ranks
     * 
     * @return void
     */
    public function calculateRanks()
    {
        // Overall rank
        $overallLeaderboards = Leaderboard::orderBy('overall_percentage', 'desc')
            ->get();

        foreach ($overallLeaderboards as $index => $entry) {
            $entry->overall_rank = $index + 1;
            $entry->save();
        }

        // Weekly rank
        $weekStart = now()->startOfWeek();
        $weeklyLeaderboards = ExamSession::where('started_at', '>=', $weekStart)
            ->selectRaw('user_id, SUM(percentage) as weekly_score')
            ->groupBy('user_id')
            ->orderByDesc('weekly_score')
            ->get();

        foreach ($weeklyLeaderboards as $index => $entry) {
            Leaderboard::where('user_id', $entry->user_id)
                ->update([
                    'weekly_rank' => $index + 1,
                    'weekly_score' => $entry->weekly_score,
                ]);
        }

        // Monthly rank
        $monthStart = now()->startOfMonth();
        $monthlyLeaderboards = ExamSession::where('started_at', '>=', $monthStart)
            ->selectRaw('user_id, SUM(percentage) as monthly_score')
            ->groupBy('user_id')
            ->orderByDesc('monthly_score')
            ->get();

        foreach ($monthlyLeaderboards as $index => $entry) {
            Leaderboard::where('user_id', $entry->user_id)
                ->update([
                    'monthly_rank' => $index + 1,
                    'monthly_score' => $entry->monthly_score,
                ]);
        }
    }

    /**
     * Get performance trend for a user (last N tests)
     * 
     * @param User $user
     * @param int $days
     * @return array
     */
    public function getPerformanceTrend(User $user, $days = 30)
    {
        $sessions = $user->examSessions()
            ->where('status', 'completed')
            ->where('finished_at', '>=', now()->subDays($days))
            ->orderBy('finished_at')
            ->get();

        return $sessions->map(function ($session) {
            return [
                'date' => $session->finished_at->format('Y-m-d'),
                'percentage' => $session->percentage,
                'score' => $session->score,
                'subject' => $session->subject->name,
            ];
        })->toArray();
    }

    /**
    
     * Get weak areas for improvement
     * 
     * @param User $user
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getWeakAreas(User $user, $limit = 5)
    {
        $answerLogs = $user->answerLogs()
            ->where('is_correct', false)
            ->join('mcqs', 'answer_logs.mcq_id', '=', 'mcqs.id')
            ->join('subjects', 'mcqs.subject_id', '=', 'subjects.id')
            ->selectRaw('subjects.id, subjects.name, COUNT(*) as wrong_count')
            ->groupBy('subjects.id', 'subjects.name')
            ->orderByDesc('wrong_count')
            ->limit($limit)
            ->get();

        return $answerLogs;
    }
    /**
     * Helper method to get score by type
     * 
     * @param Leaderboard $entry
     * @param string $type
     * @return float
     */
    private function getScoreByType($entry, $type)
    {
        return match($type) {
            'weekly' => $entry->weekly_score,
            'monthly' => $entry->monthly_score,
            default => $entry->overall_percentage,
        };
    }
}