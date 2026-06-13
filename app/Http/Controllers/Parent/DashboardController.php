<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ExamSession;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show parent dashboard
     */
    public function index()
    {
        $parent = auth()->user();

        // ✅ MINIMAL FIX: Find students in same batch as parent (if parent has batch field)
        // This assumes the relationship is by batch/class, not explicit DB link yet
        $children = User::where('role', 'student')
            ->where('batch', $parent->batch) // Match by batch
            ->get();

        $stats = [
            'total_children' => $children->count(),
            'average_child_score' => 0,
            'total_tests' => 0,
        ];

        // Calculate stats from actual exam data
        if ($children->count() > 0) {
            $childIds = $children->pluck('id');
            $allSessions = ExamSession::whereIn('user_id', $childIds)
                ->where('status', 'completed')
                ->get();
            
            $stats['total_tests'] = $allSessions->count();
            $stats['average_child_score'] = $allSessions->count() > 0 
                ? round($allSessions->avg('percentage') ?? 0, 2)
                : 0;
        }

        return view('parent.dashboard', compact('stats', 'children'));
    }

    /**
     * List children
     */
    public function children()
    {
        $parent = auth()->user();
        
        // ✅ Get children by batch
        $children = User::where('role', 'student')
            ->where('batch', $parent->batch)
            ->with('examSessions')
            ->get()
            ->map(function ($child) {
                $sessions = $child->examSessions()
                    ->where('status', 'completed')
                    ->get();
                
                return [
                    'user' => $child,
                    'total_tests' => $sessions->count(),
                    'average_score' => $sessions->count() > 0 ? round($sessions->avg('percentage') ?? 0, 2) : 0,
                    'tests_passed' => $sessions->where('percentage', '>=', 50)->count(),
                ];
            });

        return view('parent.children', compact('children'));
    }

    /**
     * View child results
     */
    public function childResults(User $child)
    {
        $parent = auth()->user();
        
        // ✅ Verify parent can view this child (by batch)
        if ($child->batch !== $parent->batch || $child->role !== 'student') {
            abort(403, 'Unauthorized');
        }

        $results = ExamSession::where('user_id', $child->id)
            ->where('status', 'completed')
            ->with('subject')
            ->latest()
            ->paginate(15);

        $analytics = [
            'total_tests' => ExamSession::where('user_id', $child->id)->count(),
            'average_score' => round(ExamSession::where('user_id', $child->id)->avg('percentage') ?? 0, 2),
            'tests_passed' => ExamSession::where('user_id', $child->id)->where('percentage', '>=', 50)->count(),
        ];

        return view('parent.child-results', compact('child', 'results', 'analytics'));
    }
}