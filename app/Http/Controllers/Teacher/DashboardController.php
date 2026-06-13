<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Mcq;
use App\Models\Subject;
use App\Models\ExamSession;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show teacher dashboard
     */
    public function index()
    {
        $teacher = auth()->user();

        // Get all MCQs created by this teacher
        $teacherMcqIds = Mcq::where('created_by', $teacher->id)->pluck('id');
        
        // ✅ Get all subjects this teacher has created MCQs for
        $teacherSubjectIds = Mcq::where('created_by', $teacher->id)
            ->distinct()
            ->pluck('subject_id');

        // ✅ Calculate students who took exams from this teacher's MCQs
        $totalStudentsTaught = ExamSession::whereIn('subject_id', $teacherSubjectIds)
            ->where('status', 'completed')
            ->distinct('user_id')
            ->count('user_id');

        $stats = [
            'total_mcqs' => Mcq::where('created_by', $teacher->id)->count(),
            'pending_review' => Mcq::where('created_by', $teacher->id)
                ->where('status', 'pending_review')
                ->count(),
            'approved_mcqs' => Mcq::where('created_by', $teacher->id)
                ->where('status', 'active')
                ->count(),
            'total_students_taught' => $totalStudentsTaught,
        ];

        $recentMcqs = Mcq::where('created_by', $teacher->id)
            ->with('subject')
            ->latest()
            ->limit(5)
            ->get();

        // ✅ Get recent exam sessions from teacher's MCQs
        $recentResults = ExamSession::whereIn('subject_id', $teacherSubjectIds)
            ->where('status', 'completed')
            ->with('user', 'subject')
            ->latest()
            ->limit(5)
            ->get();

        return view('teacher.dashboard', compact('stats', 'recentMcqs', 'recentResults'));
    }

    /**
     * List teacher's MCQs
     */
    public function mcqs()
    {
        $mcqs = Mcq::where('created_by', auth()->id())
            ->with('subject')
            ->latest()
            ->paginate(15);

        return view('teacher.mcqs.index', compact('mcqs'));
    }

    /**
     * Show create MCQ form
     */
    public function createMcq()
    {
        $subjects = Subject::where('status', 'active')->get();
        return view('teacher.mcqs.create', compact('subjects'));
    }

    /**
     * Store new MCQ
     */
    public function storeMcq(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
            'difficulty' => 'required|in:easy,medium,hard',
            'explanation' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'pending_review';

        Mcq::create($validated);

        return redirect()->route('teacher.mcqs')->with('success', 'MCQ created successfully! Awaiting admin approval.');
    }

    /**
     * List student classes
     */
    public function classes()
    {
        return view('teacher.classes');
    }

    /**
     * View exam results from teacher's MCQs
     */
    public function results()
    {
        $teacher = auth()->user();
        
        // ✅ Get all subjects this teacher teaches
        $teacherSubjectIds = Mcq::where('created_by', $teacher->id)
            ->distinct()
            ->pluck('subject_id');

        // ✅ Get results for exams from this teacher's MCQs
        $results = ExamSession::whereIn('subject_id', $teacherSubjectIds)
            ->where('status', 'completed')
            ->with('user', 'subject')
            ->latest()
            ->paginate(20);

        return view('teacher.results', compact('results'));
    }

    /**
     * Singular alias to handle the exact route configuration mapping safely.
     */
    public function result()
    {
        return $this->results();
    }

    /**
     * Show detailed review sheet for an individual exam session
     */
    public function showResult($id)
    {
        $examSession = ExamSession::findOrFail($id);
        $results = [];

        // 🟢 FIXED: Dynamically check which relationship exists on your model to prevent 500 errors
        if (method_exists($examSession, 'examAnswers')) {
            $examSession->load('examAnswers.mcq');
            $answersCollection = $examSession->examAnswers;
        } elseif (method_exists($examSession, 'responses')) {
            $examSession->load('responses.mcq');
            $answersCollection = $examSession->responses;
        } elseif (method_exists($examSession, 'userAnswers')) {
            $examSession->load('userAnswers.mcq');
            $answersCollection = $examSession->userAnswers;
        } else {
            // Fallback to avoid crashing if relationship name is completely custom
            $answersCollection = collect();
        }

        // Map the recovered relationship records into your view format
        foreach ($answersCollection as $answer) {
            $questionData = $answer->mcq ?? $answer->question ?? null; 

            if ($questionData) {
                $results[] = [
                    'question'       => $questionData->question,
                    'option_a'       => $questionData->option_a,
                    'option_b'       => $questionData->option_b,
                    'option_c'       => $questionData->option_c,
                    'option_d'       => $questionData->option_d,
                    'correct_answer' => $questionData->correct_answer,
                    'student_answer' => $answer->student_answer ?? $answer->selected_option ?? $answer->answer ?? null,
                    'explanation'    => $questionData->explanation ?? 'No explanation available.',
                ];
            }
        }

        // Always make sure user and subject are loaded for the headers
        $examSession->load(['user', 'subject']);

        return view('teacher.result_detail', compact('examSession', 'results'));
    }
}