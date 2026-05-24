<?php

namespace App\Services;

use App\Models\ExamSession;
use App\Models\User;
use App\Models\Subject;
use App\Models\AnswerLog;

class ExamSessionService
{
    protected $mcqRandomizationService;
    protected $scoringService;

    public function __construct(
        McqRandomizationService $mcqRandomizationService,
        ScoringService $scoringService
    ) {
        $this->mcqRandomizationService = $mcqRandomizationService;
        $this->scoringService = $scoringService;
    }

    /**
     * Create a new exam session
     * 
     * @param User $user
     * @param Subject $subject
     * @param int $questionCount
     * @param int $durationMinutes
     * @return ExamSession
     */
    public function createSession(User $user, Subject $subject, $questionCount = 10, $durationMinutes = 20)
    {
        $session = ExamSession::create([
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'total_questions' => $questionCount,
            'duration_minutes' => $durationMinutes,
            'started_at' => now(),
            'status' => 'ongoing',
            'is_locked' => false,
        ]);

        // Get randomized MCQs
        $mcqs = $this->mcqRandomizationService->getRandomMcqs($subject->id, $questionCount);

        // Store MCQ order in answer logs (without answers yet)
        $mcqs->each(function ($mcq, $index) use ($session) {
            AnswerLog::create([
                'exam_session_id' => $session->id,
                'user_id' => $session->user_id,
                'mcq_id' => $mcq->id,
                'correct_answer' => $mcq->correct_answer,
                'question_order' => $index,
            ]);
        });

        return $session;
    }

    /**
     * Lock session after start button is clicked
     * 
     * @param ExamSession $session
     * @return ExamSession
     */
    public function lockSession(ExamSession $session)
    {
        $session->update(['is_locked' => true]);
        return $session;
    }

    /**
     * Submit an answer for a question
     * 
     * @param ExamSession $session
     * @param int $mcqId
     * @param string|null $selectedAnswer
     * @param int $timeTaken
     * @return AnswerLog
     */
    public function submitAnswer(ExamSession $session, $mcqId, $selectedAnswer = null, $timeTaken = 0)
    {
        $answerLog = $session->answerLogs()
            ->where('mcq_id', $mcqId)
            ->firstOrFail();

        $answerLog->update([
            'selected_answer' => $selectedAnswer,
            'time_taken_seconds' => $timeTaken,
        ]);

        // Mark answer as correct or incorrect
        $this->scoringService->markAnswer($answerLog);

        return $answerLog;
    }

    /**
     * Submit entire exam
     * 
     * @param ExamSession $session
     * @return ExamSession
     */
    public function submitExam(ExamSession $session)
    {
        // Calculate final scores
        $session = $this->scoringService->updateSessionScore($session);

        // Update leaderboard
        app(AnalyticsService::class)->updateLeaderboard($session->user);

        return $session;
    }

    /**
     * Auto-submit exam when time expires
     * 
     * @param ExamSession $session
     * @return ExamSession
     */
    public function autoSubmitExam(ExamSession $session)
    {
        $session->update([
            'status' => 'expired',
            'finished_at' => now(),
        ]);

        return $this->submitExam($session);
    }

    /**
     * Check if session time has expired
     * 
     * @param ExamSession $session
     * @return bool
     */
    public function isSessionExpired(ExamSession $session)
    {
        if ($session->status !== 'ongoing') {
            return false;
        }

        $elapsedMinutes = $session->started_at->diffInMinutes(now());
        return $elapsedMinutes >= $session->duration_minutes;
    }

    /**
     * Get time remaining in seconds
     * 
     * @param ExamSession $session
     * @return int
     */
    public function getTimeRemaining(ExamSession $session)
    {
        $totalSeconds = $session->duration_minutes * 60;
        $elapsedSeconds = $session->started_at->diffInSeconds(now());
        $remaining = $totalSeconds - $elapsedSeconds;

        return max(0, $remaining);
    }

    /**
     * Get exam questions with randomized options
     * 
     * @param ExamSession $session
     * @return array
     */
    public function getExamQuestions(ExamSession $session)
    {
        return $session->answerLogs()
            ->with('mcq')
            ->orderBy('question_order')
            ->get()
            ->map(function ($answerLog) {
                $mcq = $answerLog->mcq;
                $randomizedOptions = $this->mcqRandomizationService->randomizeOptions($mcq);

                return [
                    'id' => $mcq->id,
                    'question' => $mcq->question,
                    'options' => $randomizedOptions['options'],
                    'selected_answer' => $answerLog->selected_answer,
                    'order' => $answerLog->question_order,
                ];
            })
            ->toArray();
    }

    /**
     * Get session summary after completion
     * 
     * @param ExamSession $session
     * @return array
     */
    public function getSessionSummary(ExamSession $session)
    {
        $answerLogs = $session->answerLogs()->with('mcq')->get();

        $results = $answerLogs->map(function ($log) {
            return [
                'question' => $log->mcq->question,
                'selected_answer' => $log->selected_answer,
                'correct_answer' => $log->correct_answer,
                'is_correct' => $log->is_correct,
                'explanation' => $log->mcq->explanation,
                'difficulty' => $log->mcq->difficulty,
            ];
        })->toArray();

        return [
            'session_id' => $session->id,
            'subject' => $session->subject->name,
            'total_questions' => $session->total_questions,
            'correct_answers' => $session->correct_answers,
            'wrong_answers' => $session->wrong_answers,
            'unanswered' => $session->unanswered,
            'percentage' => $session->percentage,
            'score' => $session->score,
            'duration' => $session->duration_minutes,
            'time_taken' => $session->time_taken_minutes,
            'results' => $results,
        ];
    }
}