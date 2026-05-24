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
        $this->middleware('auth');
    }

    /**
     * Show exam results
     */
    public function show(ExamSession $session)
    {
        // Check authorization
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if exam is completed
        if ($session->status !== 'completed' && $session->status !== 'expired') {
            return redirect("/exam/session/{$session->id}/questions");
        }

        // Get summary
        $summary = $this->examSessionService->getSessionSummary($session);
        $scoreByDifficulty = $this->scoringService->getScoreByDifficulty($session);
        $grade = $this->scoringService->getGrade($session->percentage);

        return view('exam.result', compact('session', 'summary', 'scoreByDifficulty', 'grade'));
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

        // You can use a PDF library like TCPDF or Dompdf here
        // For now, returning JSON
        return response()->json([
            'message' => 'PDF download functionality coming soon',
            'summary' => $summary,
            'grade' => $grade,
        ]);
    }
}