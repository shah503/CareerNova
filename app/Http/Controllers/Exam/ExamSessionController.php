<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\Subject;
use App\Services\ExamSessionService;
use App\Services\SettingService;
use Illuminate\Http\Request;

class ExamSessionController extends Controller
{
    protected $examSessionService;
    protected $settingService;

    public function __construct(ExamSessionService $examSessionService, SettingService $settingService)
    {
        $this->examSessionService = $examSessionService;
        $this->settingService = $settingService;
    }

    /**
     * Show available subjects for exam selection
     */
    public function selectSubject()
    {
        if (!$this->settingService->isSystemActive()) {
            return redirect('/student/dashboard')->with('error', 'System is currently under maintenance.');
        }

        $subjects = Subject::where('status', 'active')->get();
        return view('exam.select-subject', compact('subjects'));
    }

    /**
     * Create new exam session or recover existing one
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question_count' => 'required|integer|min:5|max:50',
            'duration_minutes' => 'required|integer|min:5|max:120',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        $user = auth()->user();

        $questionCount = (int) $validated['question_count'];
        $durationMinutes = (int) $validated['duration_minutes'];

        // ✅ Use recovery logic - returns existing session or creates new one
        $session = $this->examSessionService->getOrCreateSession(
            $user,
            $subject,
            $questionCount,
            $durationMinutes
        );

        return redirect("/exam/session/{$session->id}/start");
    }

    /**
     * Start exam (lock session)
     */
    public function start(ExamSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        if ($session->is_locked) {
            return redirect("/exam/session/{$session->id}/questions");
        }

        $this->examSessionService->lockSession($session);

        return redirect("/exam/session/{$session->id}/questions");
    }

    /**
     * Display exam questions
     */
    public function questions(ExamSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        if ($this->examSessionService->isSessionExpired($session)) {
            return $this->autoSubmit($session);
        }

        $questions = $this->examSessionService->getExamQuestions($session);
        $timeRemaining = $this->examSessionService->getTimeRemaining($session);

        return view('exam.questions', compact('session', 'questions', 'timeRemaining'));
    }

    /**
     * Submit a single answer (AJAX)
     */
    public function submitAnswer(Request $request, ExamSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'mcq_id' => 'required|exists:mcqs,id',
            'selected_answer' => 'nullable|in:A,B,C,D',
            'time_taken' => 'nullable|integer',
        ]);

        try {
            $answerLog = $this->examSessionService->submitAnswer(
                $session,
                (int) $validated['mcq_id'],
                $validated['selected_answer'],
                (int) ($validated['time_taken'] ?? 0)
            );

            return response()->json([
                'success' => true,
                'is_correct' => $answerLog->is_correct,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Submit entire exam - PERMANENTLY CLOSES SESSION
     */
    public function submit(Request $request, ExamSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        // ✅ PERMANENTLY CLOSE SESSION
        $session = $this->examSessionService->submitExam($session);

        return redirect("/exam/session/{$session->id}/result");
    }

    /**
     * Auto-submit when time expires - PERMANENTLY CLOSES SESSION
     */
    public function autoSubmit(ExamSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        // ✅ PERMANENTLY CLOSE SESSION
        $session = $this->examSessionService->autoSubmitExam($session);

        return redirect("/exam/session/{$session->id}/result");
    }

    /**
     * Get time remaining (AJAX)
     */
    public function getTimeRemaining(ExamSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $timeRemaining = $this->examSessionService->getTimeRemaining($session);
        $isExpired = $timeRemaining <= 0;

        return response()->json([
            'time_remaining' => $timeRemaining,
            'is_expired' => $isExpired,
        ]);
    }

    /**
     * Get exam progress (AJAX)
     */
    public function getProgress(ExamSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $answerLogs = $session->answerLogs()->get();
        $answered = $answerLogs->whereNotNull('selected_answer')->count();
        $unanswered = $answerLogs->whereNull('selected_answer')->count();

        return response()->json([
            'answered' => $answered,
            'unanswered' => $unanswered,
            'total' => $answerLogs->count(),
            'progress_percentage' => ($answered / $answerLogs->count()) * 100,
        ]);
    }
}