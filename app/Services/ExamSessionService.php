<?php

namespace App\Services;

use App\Models\ExamSession;
use App\Models\User;
use App\Models\Subject;
use App\Models\AnswerLog;
use App\Models\Mcq;
use Carbon\Carbon;

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
     * Get or create exam session with recovery logic
     * If exam is in progress, return existing session
     * If exam is completed, create new session
     */
    public function getOrCreateSession(User $user, Subject $subject, $questionCount = 10, $durationMinutes = 20)
    {
        // Check for existing ongoing session
        $existingSession = ExamSession::where('user_id', $user->id)
            ->where('subject_id', $subject->id)
            ->where('status', 'ongoing')
            ->first();

        if ($existingSession) {
            // Session recovery - return existing session
            return $existingSession;
        }

        // Create completely new session
        return $this->createSession($user, $subject, $questionCount, $durationMinutes);
    }

    /**
     * Create a completely new exam session
     */
    public function createSession(User $user, Subject $subject, $questionCount = 10, $durationMinutes = 20)
    {
        $questionCount = (int) $questionCount;
        $durationMinutes = (int) $durationMinutes;

        // Get randomized MCQs
        $mcqs = $this->mcqRandomizationService->getRandomMcqs($subject->id, $questionCount);
        
        // Lock MCQ sequence permanently
        $mcqSequence = $mcqs->pluck('id')->toArray();

        $session = ExamSession::create([
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'total_questions' => $questionCount,
            'duration_minutes' => $durationMinutes,
            'mcq_sequence' => json_encode($mcqSequence),
            'started_at' => now(),
            'expires_at' => now()->addMinutes($durationMinutes),
            'status' => 'ongoing',
            'is_locked' => false,
        ]);

        // Pre-create answer logs with REQUIRED user_id
        foreach ($mcqs as $index => $mcq) {
            AnswerLog::create([
                'exam_session_id' => $session->id,
                'user_id' => $user->id,  // ✅ CRITICAL
                'mcq_id' => $mcq->id,
                'correct_answer' => $mcq->correct_answer,
                'selected_answer' => null,
                'is_correct' => false,
                'time_taken_seconds' => 0,
                'question_order' => $index,
            ]);
        }

        return $session;
    }

    /**
     * Lock session after start
     */
    public function lockSession(ExamSession $session)
    {
        $session->update([
            'is_locked' => true,
            'started_at' => now(),
        ]);
        return $session;
    }

    /**
     * Submit answer
     */
    public function submitAnswer(ExamSession $session, $mcqId, $selectedAnswer = null, $timeTaken = 0)
    {
        $mcqId = (int) $mcqId;
        $timeTaken = (int) $timeTaken;

        $answerLog = $session->answerLogs()
            ->where('mcq_id', $mcqId)
            ->firstOrFail();

        $mcq = Mcq::findOrFail($mcqId);

        $answerLog->update([
            'selected_answer' => $selectedAnswer,
            'time_taken_seconds' => $timeTaken,
            'is_correct' => $selectedAnswer === $mcq->correct_answer,
        ]);

        return $answerLog;
    }

    /**
     * Submit entire exam - PERMANENTLY CLOSES SESSION
     */
    public function submitExam(ExamSession $session)
    {
        $answerLogs = $session->answerLogs()->get();

        $correctAnswers = $answerLogs->where('is_correct', true)->count();
        $wrongAnswers = $answerLogs->where('is_correct', false)->where('selected_answer', '!=', null)->count();
        $unanswered = $answerLogs->where('selected_answer', null)->count();
        $totalQuestions = $answerLogs->count();
        $percentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

        // ✅ PERMANENTLY CLOSE SESSION
        $session->update([
            'status' => 'completed',  // ✅ Session is now CLOSED
            'finished_at' => now(),
            'score' => $correctAnswers,
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $wrongAnswers,
            'unanswered' => $unanswered,
            'percentage' => round($percentage, 2),
            'time_taken_minutes' => $session->started_at->diffInMinutes(now()),
        ]);

        return $session;
    }

    /**
     * Auto-submit when time expires - PERMANENTLY CLOSES SESSION
     */
    public function autoSubmitExam(ExamSession $session)
    {
        $session->update([
            'status' => 'expired',  // ✅ Session is now CLOSED
            'finished_at' => now(),
        ]);

        return $this->submitExam($session);
    }

    /**
     * Check if session expired
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
     */
    public function getTimeRemaining(ExamSession $session)
    {
        $totalSeconds = $session->duration_minutes * 60;
        $elapsedSeconds = $session->started_at->diffInSeconds(now());
        $remaining = $totalSeconds - $elapsedSeconds;

        return max(0, $remaining);
    }

    /**
     * Get exam questions
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
     * Get session summary
     */
    public function getSessionSummary(ExamSession $session)
    {
        $answerLogs = $session->answerLogs()->with('mcq')->orderBy('question_order')->get();

        $results = $answerLogs->map(function ($log) {
            return [
                'question' => $log->mcq->question,
                'selected_answer' => $log->selected_answer,
                'correct_answer' => $log->correct_answer,
                'is_correct' => $log->is_correct,
                'explanation' => $log->mcq->explanation,
                'difficulty' => $log->mcq->difficulty,
                'option_a' => $log->mcq->option_a,
                'option_b' => $log->mcq->option_b,
                'option_c' => $log->mcq->option_c,
                'option_d' => $log->mcq->option_d,
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