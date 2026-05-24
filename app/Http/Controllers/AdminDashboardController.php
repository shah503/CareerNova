<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mcq;
use App\Models\ExamSession;
use App\Models\McqReport;
use App\Models\AiMcqGeneration;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Main admin dashboard
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'total_mcqs' => Mcq::count(),
            'verified_mcqs' => Mcq::where('verified', true)->count(),
            'pending_mcqs' => Mcq::where('needs_review', true)->count(),
            'total_tests' => ExamSession::count(),
            'pending_reports' => McqReport::where('status', 'pending')->count(),
            'ai_generations' => AiMcqGeneration::count(),
        ];

        $recentReports = McqReport::where('status', 'pending')
            ->with('user', 'mcq')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReports'));
    }

    /**
     * MCQ management
     */
    public function mcqs()
    {
        $mcqs = Mcq::with('teacher', 'approver')
            ->latest()
            ->paginate(15);

        return view('admin.mcqs.index', compact('mcqs'));
    }

    /**
     * Verify MCQ
     */
    public function verifyMcq($id)
    {
        $mcq = Mcq::findOrFail($id);

        $mcq->update([
            'verified' => true,
            'needs_review' => false,
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        return redirect()->back()->with('success', 'MCQ verified');
    }

    /**
     * Flag MCQ for review
     */
    public function flagMcq($id)
    {
        $mcq = Mcq::findOrFail($id);

        $mcq->update([
            'needs_review' => true,
            'verified' => false
        ]);

        return redirect()->back()->with('success', 'MCQ flagged for review');
    }

    /**
     * Delete MCQ
     */
    public function deleteMcq($id)
    {
        $mcq = Mcq::findOrFail($id);
        $mcq->delete();

        return redirect()->back()->with('success', 'MCQ deleted');
    }
}