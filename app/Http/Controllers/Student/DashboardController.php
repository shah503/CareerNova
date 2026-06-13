<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\Subject;
use App\Services\ScoringService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $scoringService;

    public function __construct(ScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * Student Dashboard with complete analytics
     */
    public function index()
    {
        $user = auth()->user();

       $analytics = $this->scoringService->getUserAnalytics($user);

        $performanceTrend = collect(
            $this->scoringService->getPerformanceTrend($user, 10)
        );

       // ✅ FIX: ensure always collection
        $trendLabels = $performanceTrend->map(
           fn($session) =>
                $session->subject->name . ' (' . $session->finished_at->format('M d') . ')'
        )->toArray();

       $trendData = $performanceTrend->pluck('percentage')->toArray();

       // Subject analytics safety
       $subjectWise = $analytics['subject_performance'] ?? [];

        $subjectLabels = [];
        $subjectData = [];

        foreach ($subjectWise as $subjectId => $performance) {
            $subject = \App\Models\Subject::find($subjectId);

            if ($subject) {
                $subjectLabels[] = $subject->name;
                $subjectData[] = round($performance['average'] ?? 0, 2);
            }
        }

        // ✅ FIX: stats mapping for Blade
        $stats = [
            'total_tests'    => $analytics['total_tests'] ?? 0,
            'completed_tests'=> $analytics['total_tests'] ?? 0,
            'average_score'  => $analytics['average_score'] ?? 0,
            'tests_passed'   => $analytics['tests_passed'] ?? 0,
        ];

        $recentSessions = \App\Models\ExamSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest('finished_at')
            ->take(10)
            ->with('subject')
            ->get();

        return view('student.dashboard', compact(
            'stats',
            'recentSessions',
            'analytics',
            'trendLabels',
            'trendData',
            'subjectLabels',
            'subjectData'
        ));
    }

    /**
     * Exam history
     */
    public function examHistory()
    {
        $user = auth()->user();
        $sessions = ExamSession::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'expired'])
            ->with('subject')
            ->orderBy('finished_at', 'desc')
            ->paginate(10);

        // Change 'student.exam-history' to match your actual blade file name:
        return view('student.exam', compact('sessions')); 
    }
    /**
     * Leaderboard
     */
    public function leaderboard()
    {
        $leaderboard = ExamSession::whereIn('status', ['completed', 'expired'])
            ->with('user', 'subject')
            ->orderBy('percentage', 'desc')
            ->limit(100)
            ->get()
            ->groupBy('subject_id');

        return view('student.leaderboard', compact('leaderboard'));
    }

    /**
     * Analytics Page - Shows aggregated student performance data
     */
    public function analytics()
    {
        $user = auth()->user();
    
        // ✅ Use AnalyticsService (NOT ScoringService) for comprehensive analytics
        $analyticsService = new \App\Services\AnalyticsService();
    
        // Get all required data from the same source as Admin Dashboard
        $analytics = $analyticsService->getStudentAnalytics($user);
        $subjectWiseAnalytics = $analyticsService->getSubjectWiseAnalytics($user);
        $weakAreas = $analyticsService->getWeakAreas($user, 5);
        $performanceTrend = $analyticsService->getPerformanceTrend($user, 30);
    
        // Prepare chart data
        $trendLabels = array_map(fn($trend) => $trend['date'], $performanceTrend);
        $trendData = array_map(fn($trend) => $trend['percentage'], $performanceTrend);
    
        $subjectLabels = array_map(fn($subject) => $subject['subject_name'], $subjectWiseAnalytics);
        $subjectData = array_map(fn($subject) => $subject['average_percentage'], $subjectWiseAnalytics);
    
        return view('student.analytics', compact(
            'analytics',
            'subjectWiseAnalytics',
            'weakAreas',
            'performanceTrend',
            'trendLabels',
            'trendData',
            'subjectLabels',
            'subjectData'
        ));
    }
}