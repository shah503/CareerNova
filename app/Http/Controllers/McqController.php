<?php

namespace App\Http\Controllers;

use App\Models\Mcq;
use App\Models\ExamSession;
use App\Models\AnswerLog;
use App\Models\Subject;
use App\Models\QuestionProgress;
use App\Services\ExamService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class McqController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    /**
     * Select subject to take exam
     */
    public function selectSubject()
    {
        $subjects = Subject::where('status', 'active')
            ->withCount(['mcqs' => function ($q) {
                $q->where('status', 'active');
            }])
            ->get();

        return view('exam.select-subject', compact('subjects'));
    }

    /**
     * Show MCQs for exam
     */
    public function index(Request $request)
    {
        $subjectId = $request->query('subject_id');

        if (!$subjectId) {
            return redirect()->route('exam.select-subject')->with('error', 'Please select a subject');
        }

        // Get active MCQs
        $mcqs = Mcq::where('subject_id', $subjectId)
            ->where('status', 'active')
            ->inRandomOrder()
            ->get();

        if ($mcqs->isEmpty()) {
            return redirect()->route('exam.select-subject')
                ->with('error', 'No questions available for this subject');
        }

        // Check if there's an existing exam session
        $existingExam = ExamSession::where('user_id', auth()->id())
            ->where('subject_id', $subjectId)
            ->where('status', 'in_progress')
            ->latest()
            ->first();

        if ($existingExam && (now()->timestamp - $existingExam->started_at->timestamp) < 3600) {
            // Resume existing exam if within 1 hour
            $recovery = $this->examService->getSessionRecoveryData($existingExam->id);
            session([
                'exam_session_id' => $existingExam->id,
                'exam_mcqs' => $mcqs->toArray(),
                'exam_subject_id' => $subjectId,
                'exam_started' => true,
                'exam_start_time' => $existingExam->started_at->timestamp,
                'exam_time_remaining' => $recovery['remaining_time'],
                'exam_answers' => $this->getExistingAnswers($existingExam->id),
            ]);

            return view('exam.index', [
                'mcqs' => $mcqs,
                'exam_started' => true,
                'exam_session_id' => $existingExam->id,
                'recovery' => $recovery,
                'is_recovery' => true,
            ]);
        }

        // Store in session
        session([
            'exam_mcqs' => $mcqs->toArray(),
            'exam_subject_id' => $subjectId,
            'exam_started' => false,
        ]);

        return view('exam.index', [
            'mcqs' => $mcqs,
            'exam_started' => false,
        ]);
    }

    /**
     * Start exam
     */
    public function startTest(Request $request)
    {
        $mcqs = session('exam_mcqs');
        $subjectId = session('exam_subject_id');

        if (!$mcqs || empty($mcqs)) {
            return redirect()->route('exam.select-subject')
                ->with('error', 'Session expired. Please select subject again.');
        }

        $totalQuestions = count($mcqs);
        $timeInSeconds = $totalQuestions * 60;

        // Create exam session
        $examSession = $this->examService->initializeExamSession(
            auth()->id(),
            $subjectId,
            collect($mcqs)
        );

        session([
            'exam_session_id' => $examSession->id,
            'exam_started' => true,
            'exam_start_time' => now()->timestamp,
            'exam_time_remaining' => $timeInSeconds,
            'exam_answers' => [],
        ]);

        return redirect()->route('exam.index', ['subject_id' => $subjectId])
            ->with('success', 'Exam started!');
    }

    /**
     * Save answer with question progress
     */
    public function saveAnswer(Request $request)
    {
        if (!session('exam_started')) {
            return response()->json(['error' => 'Exam not started'], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|integer',
            'answer' => 'nullable|in:A,B,C,D',
            'question_number' => 'required|integer',
            'mark_for_review' => 'boolean',
        ]);

        $examSessionId = session('exam_session_id');
        $answers = session('exam_answers', []);
        $answers[$validated['question_id']] = $validated['answer'];
        session(['exam_answers' => $answers]);

        // Update question progress
        if ($validated['answer']) {
            $status = $validated['mark_for_review'] ? 'answered_marked' : 'answered';
        } else {
            $status = $validated['mark_for_review'] ? 'marked' : 'visited';
        }

        $this->examService->updateQuestionProgress(
            $examSessionId,
            $validated['question_number'],
            $status,
            $validated['answer']
        );

        // Update last saved time
        $examSession = ExamSession::find($examSessionId);
        $examSession->update(['last_saved_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Answer saved',
            'timestamp' => now()->format('H:i:s A'),
        ]);
    }

    /**
     * Mark question for review
     */
    public function markForReview(Request $request)
    {
        $validated = $request->validate([
            'question_number' => 'required|integer',
        ]);

        $examSessionId = session('exam_session_id');
        $this->examService->markForReview($examSessionId, $validated['question_number']);

        return response()->json(['success' => true]);
    }

    /**
     * Get exam progress
     */
    public function getProgress(Request $request)
    {
        $examSessionId = session('exam_session_id');
        $questionProgress = QuestionProgress::where('exam_session_id', $examSessionId)
            ->get();

        $stats = [
            'answered' => $questionProgress->whereIn('status', ['answered', 'answered_marked'])->count(),
            'marked' => $questionProgress->whereIn('status', ['marked', 'answered_marked'])->count(),
            'not_visited' => $questionProgress->where('status', 'not_visited')->count(),
            'visited' => $questionProgress->where('status', 'visited')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Submit exam
     */
    public function submitTest(Request $request)
    {
        $mcqs = session('exam_mcqs');
        $answers = session('exam_answers', []);
        $examSessionId = session('exam_session_id');

        if (!$mcqs) {
            return redirect()->route('exam.select-subject')
                ->with('error', 'Exam session expired');
        }

        // Calculate results
        $calculation = $this->examService->calculateResults($examSessionId, $answers, $mcqs);

        // Save results
        $this->examService->saveResults($examSessionId, $calculation, $answers, $mcqs);

        // Clear session
        session()->forget([
            'exam_mcqs',
            'exam_started',
            'exam_start_time',
            'exam_answers',
            'exam_subject_id',
            'exam_time_remaining',
            'exam_session_id',
        ]);

        return redirect()->route('exam.result', $examSessionId)
            ->with('success', 'Exam submitted successfully!');
    }

    /**
     * Show exam result
     */
    public function result(ExamSession $examSession)
    {
        if ($examSession->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $answerLogs = AnswerLog::where('exam_session_id', $examSession->id)
            ->with('mcq')
            ->get();

        $results = $answerLogs->map(function($log) {
            return [
                'question' => $log->mcq->question,
                'option_a' => $log->mcq->option_a,
                'option_b' => $log->mcq->option_b,
                'option_c' => $log->mcq->option_c,
                'option_d' => $log->mcq->option_d,
                'student_answer' => $log->selected_answer,
                'correct_answer' => $log->correct_answer,
                'explanation' => $log->mcq->explanation,
                'is_correct' => $log->is_correct,
            ];
        });

        // Get user's ranking
        $userRank = auth()->user()->leaderboard?->rank ?? 'N/A';

        return view('exam.result', compact('examSession', 'results', 'userRank'));
    }

    /**
     * Get existing answers for recovery
     */
    private function getExistingAnswers($examSessionId)
    {
        $answerLogs = AnswerLog::where('exam_session_id', $examSessionId)->get();
        $answers = [];

        foreach ($answerLogs as $log) {
            $answers[$log->mcq_id] = $log->selected_answer;
        }

        return $answers;
    }
}