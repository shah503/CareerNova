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

        // TODO: Implement parent_child relationship in database
        $children = collect([]);

        $stats = [
            'total_children' => $children->count(),
            'average_child_score' => 0,
            'total_tests' => 0,
        ];

        return view('parent.dashboard', compact('stats', 'children'));
    }

    /**
     * List children
     */
    public function children()
    {
        $children = collect([]);
        return view('parent.children', compact('children'));
    }

    /**
     * View child results
     */
    public function childResults(User $child)
    {
        $results = ExamSession::where('user_id', $child->id)
            ->with('mcq')
            ->latest()
            ->paginate(15);

        return view('parent.child-results', compact('child', 'results'));
    }
}