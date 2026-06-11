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
        $performanceTrend = $this->scoringService->getPerformanceTrend($user, 10);

        // Prepare chart data
        $trendLabels = $performanceTrend->map(fn($session) => $session->subject->name . ' (' . $session->finished_at->format('M d') . ')')->toArray();
        $trendData = $performanceTrend->pluck('percentage')->toArray();

        // Subject-wise performance chart
        $subjectLabels = [];
        $subjectData = [];
        foreach ($analytics['subject_performance'] as $subjectId => $performance) {
            $subject = Subject::find($subjectId);
            if ($subject) {
                $subjectLabels[] = $subject->name;
                $subjectData[] = round($performance['average'], 2);
            }
        }

        return view('student.dashboard', compact(
            'analytics',
            'performanceTrend',
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
     * Analytics
     */
    public function analytics()
    {
        $user = auth()->user();
        $analytics = $this->scoringService->getUserAnalytics($user);
        $performanceTrend = $this->scoringService->getPerformanceTrend($user, 20);

        return view('student.analytics', compact('analytics', 'performanceTrend'));
    }
}