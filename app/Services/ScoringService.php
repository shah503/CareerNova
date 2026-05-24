<?php

namespace App\Services;

use App\Models\ExamSession;
use App\Models\AnswerLog;

class ScoringService
{
    /**
     * Calculate score for an exam session
     * 
     * @param ExamSession $session
     * @return array
     */
    public function calculateScore(ExamSession $session)
    {
        $answerLogs = $session->answerLogs()->get();

        $totalQuestions = $answerLogs->count();
        $correctAnswers = $answerLogs->where('is_correct', true)->count();
        $wrongAnswers = $answerLogs->where('is_correct', false)->whereNotNull('selected_answer')->count();
        $unanswered = $answerLogs->whereNull('selected_answer')->count();

        // Calculate percentage
        $percentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

        // Calculate score (e.g., 1 point per correct answer)
        $score = $correctAnswers;

        return [
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $wrongAnswers,
            'unanswered' => $unanswered,
            'percentage' => round($percentage, 2),
            'score' => $score,
        ];
    }

    /**
     * Mark answer as correct or incorrect
     * 
     * @param AnswerLog $answerLog
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
     * 
     * @param ExamSession $session
     * @return ExamSession
     */
    public function updateSessionScore(ExamSession $session)
    {
        $scoreData = $this->calculateScore($session);

        $session->update([
            'total_questions' => $scoreData['total_questions'],
            'correct_answers' => $scoreData['correct_answers'],
            'wrong_answers' => $scoreData['wrong_answers'],
            'unanswered' => $scoreData['unanswered'],
            'percentage' => $scoreData['percentage'],
            'score' => $scoreData['score'],
            'status' => 'completed',
            'finished_at' => now(),
        ]);

        return $session;
    }

    /**
     * Get score breakdown by difficulty
     * 
     * @param ExamSession $session
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
                    'percentage' => ($correct / $total) * 100,
                ];
            }
        }

        return $breakdown;
    }

    /**
     * Get performance grade
     * 
     * @param float $percentage
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