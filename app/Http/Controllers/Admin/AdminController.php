<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ExamSession;
use App\Models\Subject;
use App\Models\Mcq;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function subjects()
    {
    return view('admin.subjects');
    }

    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'total_parents' => User::where('role', 'parent')->count(),
            'total_subjects' => Subject::count(),
            'total_mcqs' => Mcq::count(),
            'pending_review' => Mcq::where('status', 'pending_review')->count(),
            'total_tests' => ExamSession::count(),
            'avg_score' => ExamSession::where('status', 'completed')->avg('percentage') ?? 0,
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * List all users
     */
    public function listUsers(Request $request)
    {
        $role = $request->query('role');
        $query = User::query();

        if ($role && in_array($role, ['student', 'teacher', 'parent', 'admin'])) {
            $query->where('role', $role);
        }

        $users = $query->paginate(15);

        return view('admin.users.list', compact('users', 'role'));
    }

    /**
     * Edit user
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,teacher,student,parent',
            'status' => 'required|in:active,inactive,suspended',
            'batch' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.list')->with('success', 'User updated successfully');
    }

    /**
     * List MCQs
     */
    public function mcqs()
    {
        $mcqs = Mcq::with('creator', 'subject')
            ->latest()
            ->paginate(15);

        return view('admin.mcqs.list', compact('mcqs'));
    }

    /**
     * Verify MCQ
     */
    public function verifyMcq(Mcq $mcq)
    {
        $mcq->update([
            'status' => 'active',
            'verified' => true,
            'needs_review' => false,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'MCQ verified successfully');
    }

    /**
     * Flag MCQ for review
     */
    public function flagMcq(Mcq $mcq)
    {
        $mcq->update([
            'status' => 'pending_review',
            'needs_review' => true,
        ]);

        return back()->with('success', 'MCQ flagged for review');
    }

    /**
     * Delete MCQ
     */
    public function deleteMcq(Mcq $mcq)
    {
        $mcq->delete();
        return back()->with('success', 'MCQ deleted successfully');
    }

    /**
     * View analytics
     */
    public function analytics()
    {
        $stats = [
            'total_tests' => ExamSession::count(),
            'total_students' => User::where('role', 'student')->count(),
            'avg_score' => ExamSession::where('status', 'completed')->avg('percentage') ?? 0,
            'passed_tests' => ExamSession::where('percentage', '>=', 50)->count(),
            'failed_tests' => ExamSession::where('percentage', '<', 50)->count(),
        ];

        $subjectStats = ExamSession::with('subject')
            ->where('status', 'completed')
            ->get()
            ->groupBy('subject_id')
            ->map(function ($sessions, $subjectId) {
                $subject = Subject::find($subjectId);
                return [
                    'name' => $subject->name ?? 'Unknown',
                    'count' => $sessions->count(),
                    'avg_score' => $sessions->avg('percentage'),
                ];
            });

        return view('admin.analytics', compact('stats', 'subjectStats'));
    }

    /**
     * System settings
     */
    public function settings()
    {
        return view('admin.settings');
    }
}