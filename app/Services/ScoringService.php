<?php

namespace App\Services;

use App\Models\ExamSession;
use App\Models\AnswerLog;
use App\Models\User;
// ❌ REMOVED: use App\Services\ScoringService; (Cannot import a class into itself)

class ScoringService
{
    /**
     * Get aggregate analytics data for a specific student.
     * Required by DashboardController@index line 26
     * * @param User $user
     * @return array
     */
    public function getUserAnalytics(User $user): array
    {
        // Fetch all completed exam sessions for this user
        $sessions = ExamSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();

        $subjectPerformance = [];

        // Group data by subject_id for the chart loop on line 36 of your controller
        foreach ($sessions as $session) {
            $subjectId = $session->subject_id;

            if (!isset($subjectPerformance[$subjectId])) {
                $subjectPerformance[$subjectId] = [
                    'total_questions' => 0,
                    'correct_answers' => 0,
                    'average'         => 0 
                ];
            }

            $subjectPerformance[$subjectId]['total_questions'] += $session->total_questions;
            $subjectPerformance[$subjectId]['correct_answers'] += $session->correct_answers;
        }

        // Calculate overall weighted averages per subject
        foreach ($subjectPerformance as $subjectId => $data) {
            $subjectPerformance[$subjectId]['average'] = $data['total_questions'] > 0 
                ? round(($data['correct_answers'] / $data['total_questions']) * 100, 2)
                : 0;
        }

        return [
            'subject_performance' => $subjectPerformance,
        ];
    }

    /**
     * Get the latest completed exam sessions to build the trend line chart.
     * Required by DashboardController@index line 27
     * * @param User $user
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getPerformanceTrend(User $user, int $limit = 10)
    {
        return ExamSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('subject') // Eager load subject relationship
            ->orderBy('finished_at', 'asc') 
            ->take($limit)
            ->get();
    }

    /**
     * Calculate score for an exam session
     * * @param ExamSession $session
     * @return array
     */
    public function calculateScore(ExamSession $session)
    {
        $answerLogs = $session->answerLogs()->get();

        $totalQuestions = $answerLogs->count();
        $correctAnswers = $answerLogs->where('is_correct', true)->count();
        
        $wrongAnswers = $answerLogs->where('is_correct', false)
            ->whereNotNull('selected_answer')
            ->count();
        
        $unanswered = $answerLogs->whereNull('selected_answer')->count();
        
        $attempted = $correctAnswers + $wrongAnswers;

        $percentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
        $score = $correctAnswers;

        return [
            'total_questions' => $totalQuestions,
            'attempted' => $attempted,
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $wrongAnswers,
            'unanswered' => $unanswered,
            'percentage' => round($percentage, 2),
            'score' => $score,
        ];
    }

    /**
     * Mark answer as correct or incorrect
     * * @param AnswerLog $answerLog
     * @return bool
     */
    public function markAnswer(AnswerLog $answerLog)
    {
        if (!$answerLog->selected_answer) {
            $answerLog->is_correct = false;
            $answerLog->save();
            return false;
        }

        $isCorrect = $answerLog->selected_answer === $answerLog->correct_answer;
        $answerLog->is_correct = $isCorrect;
        $answerLog->save();

        return $isCorrect;
    }

    /**
     * Update exam session with final scores
     * * @param ExamSession $session
     * @return ExamSession
     */
    public function updateSessionScore(ExamSession $session)
    {
        $scoreData = $this->calculateScore($session);

        $session->update([
            'total_questions' => $scoreData['total_questions'],
            'correct_answers' => $scoreData['correct_answers'],
            'wrong_answers' => $scoreData['wrong_answers'],
            'unanswered_count' => $scoreData['unanswered'],
            'percentage' => $scoreData['percentage'],
            'score' => $scoreData['score'],
            'status' => 'completed',
            'finished_at' => now(),
        ]);

        return $session;
    }

    /**
     * Get score breakdown by difficulty
     * * @param ExamSession $session
     * @return array
     */
    public function getScoreByDifficulty(ExamSession $session)
    {
        $answerLogs = $session->answerLogs()
            ->join('mcqs', 'answer_logs.mcq_id', '=', 'mcqs.id')
            ->select('answer_logs.*', 'mcqs.difficulty')
            ->get();

        $breakdown = [];

        foreach (['easy', 'medium', 'hard'] as $difficulty) {
            $questions = $answerLogs->where('difficulty', $difficulty);

            if ($questions->count() > 0) {
                $correct = $questions->where('is_correct', true)->count();
                $total = $questions->count();

                $breakdown[$difficulty] = [
                    'total' => $total,
                    'correct' => $correct,
                    'incorrect' => $total - $correct,
                    'percentage' => ($correct / $total) * 100,
                ];
            }
        }

        return $breakdown;
    }

    /**
     * Get performance grade
     * * @param float $percentage
     * @return array
     */
    public function getGrade($percentage)
    {
        if ($percentage >= 90) {
            return ['grade' => 'A+', 'color' => 'success', 'remarks' => 'Excellent'];
        } elseif ($percentage >= 80) {
            return ['grade' => 'A', 'color' => 'success', 'remarks' => 'Very Good'];
        } elseif ($percentage >= 70) {
            return ['grade' => 'B', 'color' => 'info', 'remarks' => 'Good'];
        } elseif ($percentage >= 60) {
            return ['grade' => 'C', 'color' => 'warning', 'remarks' => 'Satisfactory'];
        } elseif ($percentage >= 50) {
            return ['grade' => 'D', 'color' => 'warning', 'remarks' => 'Needs Improvement'];
        } else {
            return ['grade' => 'F', 'color' => 'danger', 'remarks' => 'Failed'];
        }
    }
}