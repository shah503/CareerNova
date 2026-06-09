<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\Subject;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService = null)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Show student dashboard
     */
    /**
     * Display the Student Dashboard Workspace with Metrics & Session Tracking.
     */
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total_tests' => ExamSession::where('user_id', $user->id)->count(),
            
            'completed_tests' => ExamSession::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
                
            'average_score' => ExamSession::where('user_id', $user->id)
                ->where('status', 'completed')
                ->avg('percentage') ?? 0,
                
            // ✅ FIXED: Calculates passing count by assessing if percentage meets/exceeds 50%
            'tests_passed' => ExamSession::where('user_id', $user->id)
                ->where('status', 'completed')
                ->where('percentage', '>=', 50) // Adjust this threshold value if your passing score is higher
                ->count(),
        ];

        $recentSessions = ExamSession::where('user_id', $user->id)
            ->with('mcq')
            ->latest()
            ->limit(5)
            ->get();

        // 🚀 SYNC: Passes data directly to your beautiful student/dashboard layout template
        return view('student.dashboard', compact('stats', 'recentSessions'));
    }

    /**
     * View all exams
     */
    public function exams()
    {
        $subjects = Subject::where('status', 'active')->get();
        return view('student.exams', compact('subjects'));
    }

    /**
     * View results
     */
    public function results()
    {
        $sessions = ExamSession::where('user_id', auth()->id())
            ->with('mcq')
            ->latest()
            ->paginate(15);

        return view('student.results', compact('sessions'));
    }

    /**
     * View analytics
     */
    public function analytics()
    {
        $user = auth()->user();
        
        $monthlyData = ExamSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get()
            ->groupBy(function ($session) {
                return $session->created_at->format('Y-m');
            })
            ->map(function ($sessions) {
                return [
                    'count' => $sessions->count(),
                    'avg_score' => $sessions->avg('percentage'),
                ];
            });

        return view('student.analytics', compact('monthlyData'));
    }
}