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

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    
    }

    /**
     * Show student dashboard
     */
    public function index()
    {
        $user = auth()->user();

        // Get analytics
       $analytics = $this->analyticsService->getStudentAnalytics($user);
        $subjectAnalytics = $this->analyticsService->getSubjectWiseAnalytics($user);
        $recentSessions = $user->examSessions()
            ->where('status', 'completed')
            ->with('subject')
           ->orderBy('finished_at', 'desc')
           ->limit(5)
            ->get();

        $userRank = $this->analyticsService->getUserRank($user);
        $performanceTrend = $this->analyticsService->getPerformanceTrend($user, 30);
        $weakAreas = collect($this->analyticsService->getWeakAreas($user, 3));

        return view('student.dashboard', compact(
            'analytics',
            'subjectAnalytics',
            'recentSessions',
            'userRank',
            'performanceTrend',
            'weakAreas'
        ));
    }

    /**
     * Show exam history
     */
    public function examHistory(Request $request)
    {
        $user = auth()->user();
        $query = $user->examSessions()->where('status', 'completed')->with('subject');

        // Filter by subject
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }

        $sessions = $query->orderBy('finished_at', 'desc')->paginate(10);
        $subjects = Subject::where('status', 'active')->get();

        return view('student.exam-history', compact('sessions', 'subjects'));
    }

    /**
     * Show leaderboard
     */
    public function leaderboard()
    {
        $leaderboard = $this->analyticsService->getLeaderboard('overall', 20);
        $weeklyLeaderboard = $this->analyticsService->getLeaderboard('weekly', 10);
        $monthlyLeaderboard = $this->analyticsService->getLeaderboard('monthly', 10);

        return view('student.leaderboard', compact('leaderboard', 'weeklyLeaderboard', 'monthlyLeaderboard'));
    }

    /**
     * View performance analytics
     */
    public function analytics()
    {
        $user = auth()->user();
        $analytics = $this->analyticsService->getStudentAnalytics($user);
        $subjectAnalytics = $this->analyticsService->getSubjectWiseAnalytics($user);
        $performanceTrend = $this->analyticsService->getPerformanceTrend($user, 90);
        $weakAreas = $this->analyticsService->getWeakAreas($user, 5);

        return view('student.analytics', compact('analytics', 'subjectAnalytics', 'performanceTrend', 'weakAreas'));
    }
}