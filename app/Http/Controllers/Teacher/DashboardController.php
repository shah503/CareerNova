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
        $teacherSubjectIds = Mcq::where('created_by', auth()->id())
                ->distinct()
                ->pluck('subject_id');
        $stats = [
            'total_mcqs' => Mcq::where('created_by', $teacher->id)->count(),
            'pending_review' => Mcq::where('created_by', $teacher->id)
                ->where('status', 'pending_review')
                ->count(),
            'approved_mcqs' => Mcq::where('created_by', $teacher->id)
                ->where('status', 'active')
                ->count(),
            // ✅ FIXED: Use correct column 'mcq_id' instead of 'exam_id'
            

            'total_students_taught' => ExamSession::whereIn('subject_id', $teacherSubjectIds)
                ->distinct('user_id')
               ->count('user_id'),
        ];

        $recentMcqs = Mcq::where('created_by', $teacher->id)
            ->with('subject')
            ->latest()
            ->limit(5)
            ->get();

        return view('teacher.dashboard', compact('stats', 'recentMcqs'));
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
     * View exam results
     */
    public function results()
    {
        $teacherMcqIds = Mcq::where('created_by', auth()->id())->pluck('id');

        $results = ExamSession::whereIn('mcq_id', $teacherMcqIds)
            ->with('user', 'mcq')
            ->latest()
            ->paginate(20);

        return view('teacher.results', compact('results'));
    }
}