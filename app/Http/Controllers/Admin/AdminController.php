<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ExamSession;
use App\Models\Subject;
use App\Models\Mcq;
use App\Services\AnalyticsService;
use App\Services\SettingService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $analyticsService;
    protected $settingService;

    public function __construct(AnalyticsService $analyticsService, SettingService $settingService)
    {
        $this->analyticsService = $analyticsService;
        $this->settingService = $settingService;
    }

    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        // Check if system is active
        $systemActive = $this->settingService->isSystemActive();

        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'total_subjects' => Subject::where('status', 'active')->count(),
            'total_mcqs' => Mcq::where('status', 'active')->count(),
            'pending_reviews' => Mcq::where('status', 'pending_review')->count(),
            'total_tests_conducted' => ExamSession::where('status', 'completed')->count(),
            'avg_score' => ExamSession::where('status', 'completed')->avg('percentage'),
        ];

        return view('admin.dashboard', compact('stats', 'systemActive'));
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
     * Edit user status
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
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,teacher,student,parent',
            'status' => 'required|in:active,inactive,suspended',
            'batch' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        $user->update($validated);

        return redirect('/admin/users')->with('success', 'User updated successfully!');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect('/admin/users')->with('success', 'User deleted successfully!');
    }

    /**
     * List MCQs pending review
     */
    public function pendingMcqs()
    {
        $mcqs = Mcq::where('status', 'pending_review')
            ->with('subject', 'creator')
            ->paginate(10);

        return view('admin.mcqs.pending', compact('mcqs'));
    }

    /**
     * Approve MCQ
     */
    public function approveMcq(Mcq $mcq)
    {
        $mcq->update(['status' => 'active']);
        return back()->with('success', 'MCQ approved!');
    }

    /**
     * Reject MCQ
     */
    public function rejectMcq(Mcq $mcq)
    {
        $mcq->update(['status' => 'inactive']);
        return back()->with('success', 'MCQ rejected!');
    }

    /**
     * View system analytics
     */
    public function analytics()
    {
        $totalTests = ExamSession::where('status', 'completed')->count();
        $totalStudents = User::where('role', 'student')->count();
        $averageScore = ExamSession::where('status', 'completed')->avg('percentage');
        $leaderboard = $this->analyticsService->getLeaderboard('overall', 10);

        return view('admin.analytics', compact('totalTests', 'totalStudents', 'averageScore', 'leaderboard'));
    }

    /**
     * Toggle system status
     */
    public function toggleSystem(Request $request)
    {
        $status = $request->query('status');

        if ($status === 'on') {
            $this->settingService->enableFeature('system_active');
            return redirect('/admin/settings')->with('success', 'System activated!');
        } else {
            $this->settingService->disableFeature('system_active');
            return redirect('/admin/settings')->with('success', 'System deactivated!');
        }
    }

    /**
     * Toggle features
     */
    public function toggleFeature(Request $request)
    {
        $feature = $request->input('feature');
        $status = $request->input('status');

        if ($status === 'enable') {
            $this->settingService->enableFeature($feature);
            return response()->json(['message' => 'Feature enabled']);
        } else {
            $this->settingService->disableFeature($feature);
            return response()->json(['message' => 'Feature disabled']);
        }
    }
}