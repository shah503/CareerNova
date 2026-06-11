<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Services\ExamSessionService;
use App\Services\ScoringService;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    protected $examSessionService;
    protected $scoringService;

    public function __construct(ExamSessionService $examSessionService, ScoringService $scoringService)
    {
        $this->examSessionService = $examSessionService;
        $this->scoringService = $scoringService;
    }

    /**
     * Show exam results - ONLY for completed sessions
     */
    public function show(ExamSession $session)
    {
        // Check authorization
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if exam is completed - MUST be completed to view results
        if ($session->status !== 'completed' && $session->status !== 'expired') {
            return redirect("/exam/session/{$session->id}/questions");
        }

        // Get grade and analytics
        $grade = $this->scoringService->getGrade($session->percentage);
        $difficultyBreakdown = $this->scoringService->getScoreByDifficulty($session);
        $answerLogs = $session->answerLogs()->with('mcq')->orderBy('question_order')->get();

        // Prepare data for charts
        $chartData = [
            'correct' => $session->correct_answers,
            'wrong' => $session->wrong_answers,
            'unanswered' => $session->unanswered,
        ];

        return view('exam.result', compact('session', 'grade', 'difficultyBreakdown', 'answerLogs', 'chartData'));
    }

    /**
     * Get answer details (AJAX)
     */
    public function getAnswerDetail(ExamSession $session, $mcqId)
    {
        if ($session->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $answerLog = $session->answerLogs()
            ->where('mcq_id', $mcqId)
            ->with('mcq')
            ->first();

        if (!$answerLog) {
            return response()->json(['error' => 'Answer not found'], 404);
        }

        return response()->json([
            'question' => $answerLog->mcq->question,
            'selected_answer' => $answerLog->selected_answer,
            'correct_answer' => $answerLog->correct_answer,
            'is_correct' => $answerLog->is_correct,
            'explanation' => $answerLog->mcq->explanation,
            'difficulty' => $answerLog->mcq->difficulty,
            'option_a' => $answerLog->mcq->option_a,
            'option_b' => $answerLog->mcq->option_b,
            'option_c' => $answerLog->mcq->option_c,
            'option_d' => $answerLog->mcq->option_d,
        ]);
    }

    /**
     * Download result PDF
     */
    public function downloadPdf(ExamSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        $summary = $this->examSessionService->getSessionSummary($session);
        $grade = $this->scoringService->getGrade($session->percentage);

        return response()->json([
            'message' => 'PDF download functionality coming soon',
            'summary' => $summary,
            'grade' => $grade,
        ]);
    }
}