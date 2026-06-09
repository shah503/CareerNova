<?php

namespace App\Services;

use App\Models\ExamSession;
use App\Models\AnswerLog;
use App\Models\QuestionProgress;
use App\Models\ExamAnalytic;
use App\Models\Leaderboard;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ExamService
{
    /**
     * Initialize exam session with question progress
     */
    public function initializeExamSession($userId, $subjectId, Collection $mcqs)
    {
        $examSession = ExamSession::create([
            'user_id' => $userId,
            'subject_id' => $subjectId,
            'total_questions' => count($mcqs),
            'status' => 'ongoing',
            'started_at' => now(),
        ]);

        // Create question progress tracking
        foreach ($mcqs as $index => $mcq) {
            QuestionProgress::create([
                'exam_session_id' => $examSession->id,
                'question_number' => $index + 1,
                'status' => 'not_visited',
            ]);
        }

        return $examSession;
    }

    /**
     * Update question progress
     */
    public function updateQuestionProgress($examSessionId, $questionNumber, $status, $answer = null)
    {
        $progress = QuestionProgress::where('exam_session_id', $examSessionId)
            ->where('question_number', $questionNumber)
            ->first();

        if ($progress) {
            $progress->update([
                'status' => $status,
                'selected_answer' => $answer,
                'last_visited_at' => now(),
            ]);
        }

        return $progress;
    }

    /**
     * Mark question for review
     */
    public function markForReview($examSessionId, $questionNumber)
    {
        $progress = QuestionProgress::where('exam_session_id', $examSessionId)
            ->where('question_number', $questionNumber)
            ->first();

        if ($progress) {
            $newStatus = $progress->status === 'answered' ? 'answered_marked' : 'marked';
            $progress->update(['status' => $newStatus]);
        }

        return $progress;
    }

    /**
     * Calculate exam results
     */
    public function calculateResults($examSessionId, $answers, $mcqs)
    {
        $mcqsCollection = collect($mcqs);
        $correctAnswers = 0;
        $unansweredCount = 0;
        $markedForReviewCount = 0;
        $results = [];

        // Get exam session
        $examSession = ExamSession::find($examSessionId);
        $startTime = $examSession->started_at->timestamp;
        $timeTaken = now()->timestamp - $startTime;

        foreach ($answers as $questionId => $studentAnswer) {
            $mcq = $mcqsCollection->firstWhere('id', $questionId);

            if (!$mcq) continue;

            $isCorrect = strtoupper($studentAnswer) === strtoupper($mcq['correct_answer']);

            if ($isCorrect) {
                $correctAnswers++;
            }

            $results[] = [
                'question_id' => $mcq['id'],
                'question' => $mcq['question'],
                'option_a' => $mcq['option_a'],
                'option_b' => $mcq['option_b'],
                'option_c' => $mcq['option_c'],
                'option_d' => $mcq['option_d'],
                'student_answer' => strtoupper($studentAnswer),
                'correct_answer' => $mcq['correct_answer'],
                'explanation' => $mcq['explanation'],
                'is_correct' => $isCorrect,
            ];
        }

        // Count unanswered
        $unansweredCount = count($mcqs) - count($answers);

        // Count marked for review
        $markedForReviewCount = QuestionProgress::where('exam_session_id', $examSessionId)
            ->whereIn('status', ['marked', 'answered_marked'])
            ->count();

        $totalQuestions = count($results);
        $percentage = ($totalQuestions > 0) ? ($correctAnswers / $totalQuestions * 100) : 0;

        return [
            'results' => $results,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'percentage' => round($percentage, 2),
            'unanswered_count' => $unansweredCount,
            'marked_for_review_count' => $markedForReviewCount,
            'time_taken' => $timeTaken,
            'is_passed' => $percentage >= 50,
        ];
    }

    /**
     * Save exam results
     */
    public function saveResults($examSessionId, $calculation, $answers, $mcqs)
    {
        $examSession = ExamSession::find($examSessionId);
        $mcqsCollection = collect($mcqs);

        // Update exam session
        $examSession->update([
            'score' => $calculation['correct_answers'],
            'correct_answers' => $calculation['correct_answers'],
            'wrong_answers' => $calculation['total_questions'] - $calculation['correct_answers'],
            'percentage' => $calculation['percentage'],
            'unanswered_count' => $calculation['unanswered_count'],
            'marked_for_review_count' => $calculation['marked_for_review_count'],
            'finished_at' => now(),
            'status' => 'completed',
            'is_submitted' => true,
        ]);

        // Store answer logs
        foreach ($calculation['results'] as $result) {
            AnswerLog::create([
                'user_id' => $examSession->user_id,
                'mcq_id' => $result['question_id'],
                'exam_session_id' => $examSession->id,
                'selected_answer' => $result['student_answer'],
                'correct_answer' => $result['correct_answer'],
                'is_correct' => $result['is_correct'],
                'time_taken' => $calculation['time_taken'],
                'visited' => true,
            ]);
        }

        // Update analytics
        $this->updateAnalytics($examSession->user_id, $examSession->subject_id, $calculation);

        // Update leaderboard
        $this->updateLeaderboard($examSession->user_id, $calculation['percentage']);

        return $examSession;
    }

    /**
     * Update student analytics
     */
    public function updateAnalytics($userId, $subjectId, $calculation)
    {
        $analytic = ExamAnalytic::firstOrCreate(
            ['user_id' => $userId, 'subject_id' => $subjectId],
            [
                'total_tests' => 0,
                'average_score' => 0,
                'tests_passed' => 0,
                'tests_failed' => 0,
                'total_questions_attempted' => 0,
                'total_correct_answers' => 0,
            ]
        );

        // Update counts
        $newTotalTests = $analytic->total_tests + 1;
        $newTotalQuestionsAttempted = $analytic->total_questions_attempted + $calculation['total_questions'];
        $newTotalCorrectAnswers = $analytic->total_correct_answers + $calculation['correct_answers'];
        $newAverageScore = ($newTotalTests > 0) ? 
            (($analytic->average_score * $analytic->total_tests + $calculation['percentage']) / $newTotalTests) : 
            $calculation['percentage'];

        $newTestsPassed = $analytic->tests_passed + ($calculation['is_passed'] ? 1 : 0);
        $newTestsFailed = $analytic->tests_failed + (!$calculation['is_passed'] ? 1 : 0);

        $analytic->update([
            'total_tests' => $newTotalTests,
            'average_score' => round($newAverageScore, 2),
            'tests_passed' => $newTestsPassed,
            'tests_failed' => $newTestsFailed,
            'total_questions_attempted' => $newTotalQuestionsAttempted,
            'total_correct_answers' => $newTotalCorrectAnswers,
        ]);

        return $analytic;
    }

    /**
     * Update leaderboard rankings
     */
    public function updateLeaderboard($userId, $score)
    {
        $leaderboard = Leaderboard::firstOrCreate(
            ['user_id' => $userId],
            ['points' => 0, 'rank' => 0]
        );

        // Add points (e.g., 10 points per percentage point)
        $points = (int)($score * 10);
        $leaderboard->increment('points', $points);

        // Update monthly/weekly scores
        $leaderboard->update([
            'weekly_score' => $leaderboard->weekly_score + $points,
            'monthly_score' => $leaderboard->monthly_score + $points,
        ]);

        // Recalculate ranks
        $this->updateRankings();

        return $leaderboard;
    }

    /**
     * Update all rankings
     */
    public function updateRankings()
    {
        $leaderboards = Leaderboard::orderByDesc('points')->get();

        foreach ($leaderboards as $index => $leaderboard) {
            $leaderboard->update(['rank' => $index + 1]);
        }
    }

    /**
     * Get session recovery data
     */
    public function getSessionRecoveryData($examSessionId)
    {
        $examSession = ExamSession::find($examSessionId);

        if (!$examSession || $examSession->status === 'completed') {
            return null;
        }

        $questionProgress = QuestionProgress::where('exam_session_id', $examSessionId)
            ->get()
            ->keyBy('question_number');

        $answeredCount = $questionProgress->whereIn('status', ['answered', 'answered_marked'])->count();
        $markedCount = $questionProgress->whereIn('status', ['marked', 'answered_marked'])->count();
        $unansweredCount = $questionProgress->where('status', 'not_visited')->count();

        $startTime = $examSession->started_at->timestamp;
        $elapsedTime = now()->timestamp - $startTime;
        $totalTime = $examSession->total_questions * 60; // 1 minute per question
        $remainingTime = max(0, $totalTime - $elapsedTime);

        return [
            'exam_session' => $examSession,
            'question_progress' => $questionProgress,
            'answered_count' => $answeredCount,
            'marked_count' => $markedCount,
            'unanswered_count' => $unansweredCount,
            'remaining_time' => $remainingTime,
            'elapsed_time' => $elapsedTime,
        ];
    }
}