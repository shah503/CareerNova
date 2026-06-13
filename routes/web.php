<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\McqApprovalController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\CsvController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboardController;
use App\Http\Controllers\McqController;
use App\Http\Controllers\ExamSelectionController; // Added missing controller import
use Illuminate\Http\Request; // ✅ FIXED: Added missing Request import
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public Guest demo landing track completely outside the auth block
Route::get('/guest/dashboard', function () {
    return "<h3>🌍 Welcome to the Guest Demo Workspace!</h3><p>You are viewing public features. Log in to take full mock examinations.</p>";
})->name('guest.dashboard');

// Authentication Routes (Laravel Breeze)
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Authenticated User Router System
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Central core switch map handling logins, registrations, and direct hits
    Route::get('/dashboard', function () {
        return match (auth()->user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'parent'  => redirect()->route('parent.dashboard'),
            default   => redirect()->route('student.dashboard'),
        };
    })->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'listUsers'])->name('users.list');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::get('/mcqs', [AdminController::class, 'mcqs'])->name('mcqs.list');
        Route::patch('/mcqs/{mcq}/verify', [AdminController::class, 'verifyMcq'])->name('mcqs.verify');
        Route::patch('/mcqs/{mcq}/flag', [AdminController::class, 'flagMcq'])->name('mcqs.flag');
        Route::delete('/mcqs/{mcq}', [AdminController::class, 'deleteMcq'])->name('mcqs.delete');
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    
        // Admin Management (create/list/delete admins)
        Route::get('/admins', [AdminManagementController::class, 'listAdmins'])->name('admins.list');
        Route::get('/admins/create', [AdminManagementController::class, 'create'])->name('admins.create');
        Route::post('/admins', [AdminManagementController::class, 'store'])->name('admins.store');
        Route::delete('/admins/{admin}', [AdminManagementController::class, 'destroyAdmin'])->name('admins.destroy');
        Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects');
        
        // MCQ Approval Routes
        Route::prefix('mcq-approval')->name('mcq-approval.')->group(function () {
            Route::get('/', [McqApprovalController::class, 'index'])->name('index');
            Route::get('/{mcq}', [McqApprovalController::class, 'show'])->name('show');
            Route::patch('/{mcq}/approve', [McqApprovalController::class, 'approve'])->name('approve');
            Route::patch('/{mcq}/reject', [McqApprovalController::class, 'reject'])->name('reject');
            Route::post('/bulk-approve', [McqApprovalController::class, 'bulkApprove'])->name('bulk-approve');
        });
    });

    // Teacher Routes
    Route::middleware('teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/mcqs', [TeacherDashboardController::class, 'mcqs'])->name('mcqs');
        Route::get('/mcqs/create', [TeacherDashboardController::class, 'createMcq'])->name('mcqs.create');
        Route::post('/mcqs', [TeacherDashboardController::class, 'storeMcq'])->name('mcqs.store');
        Route::get('/classes', [TeacherDashboardController::class, 'classes'])->name('classes');
        Route::get('/result', [TeacherDashboardController::class, 'result'])->name('result');
    
        // 🟢 FIXED: Removed the extra /teacher prefix and simplified the route name
        Route::get('/result/{id}', [TeacherDashboardController::class, 'showResult'])->name('result.show');
    });

    // CSV Import Routes
    Route::middleware('teacher')->prefix('teacher/csv')->name('teacher.csv.')->group(function () {
        Route::get('/download-template', [CsvController::class, 'downloadTemplate'])->name('download-template');
        Route::post('/import', [CsvController::class, 'import'])->name('import');
    });

    // Student Dashboard & Exam Processing Routes
Route::middleware('student')->prefix('student')->name('student.')->group(function () {
    // Dashboard Panels
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/exams', [StudentDashboardController::class, 'exams'])->name('exams');
    Route::get('/result', [StudentDashboardController::class, 'examHistory'])->name('result');
    Route::get('/analytics', [StudentDashboardController::class, 'analytics'])->name('analytics');
});

// ========================================================================
    // Unified Student Exam Blocks (Fixed Overwrite)
    // ========================================================================
    Route::middleware('student')->prefix('exam')->name('exam.')->group(function () {
        // Selection & Setup (ExamSelectionController)
        Route::get('/selection', [ExamSelectionController::class, 'index'])->name('selection');
        Route::post('/create-custom', [ExamSelectionController::class, 'createCustomTest'])->name('create-custom');
        Route::post('/load-preset/{testPackage}', [ExamSelectionController::class, 'loadPresetTest'])->name('load-preset');

        // Active MCQ Testing Flow (McqController)
        Route::get('/select-subject', [McqController::class, 'selectSubject'])->name('select-subject');
        Route::post('/start', [McqController::class, 'startTest'])->name('start');
        Route::get('/index', [McqController::class, 'index'])->name('index');
        Route::post('/answer', [McqController::class, 'saveAnswer'])->name('answer');
        Route::post('/submit', [McqController::class, 'submitTest'])->name('submit');
        
        // Progress & Results
        Route::get('/progress', [McqController::class, 'getProgress'])->name('progress');
        Route::get('/result/{examSession}', [McqController::class, 'result'])->name('result'); // ✅ FIXED: Changed 'results' back to 'result'
        
        // Reviews
        Route::post('/mark-review', [McqController::class, 'markForReview'])->name('mark-review');
        Route::post('/set-review-mode', function (Request $request) {
            session(['review_mode' => $request->input('review_mode', false)]);
            return response()->json(['success' => true]);
        })->name('set-review-mode');
    });

    // Parent Routes - already exist, just clarifying
    Route::middleware('parent')->prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/children', [ParentDashboardController::class, 'children'])->name('children');
        Route::get('/children/{child}/results', [ParentDashboardController::class, 'childResults'])->name('child.results');
    });

    // ✅ FIXED & MERGED: All Exam Routes under a single group block
    Route::middleware('student')->prefix('exam')->name('exam.')->group(function () {
        // Selection & Setup (ExamSelectionController)
        Route::get('/selection', [ExamSelectionController::class, 'index'])->name('selection');
        Route::post('/create-custom', [ExamSelectionController::class, 'createCustomTest'])->name('create-custom');
        Route::post('/load-preset/{testPackage}', [ExamSelectionController::class, 'loadPresetTest'])->name('load-preset');

        // Active MCQ Testing Flow (McqController)
        Route::get('/select-subject', [McqController::class, 'selectSubject'])->name('select-subject');
        Route::post('/start', [McqController::class, 'startTest'])->name('start');
        Route::get('/index', [McqController::class, 'index'])->name('index');
        Route::post('/answer', [McqController::class, 'saveAnswer'])->name('answer');
        Route::post('/submit', [McqController::class, 'submitTest'])->name('submit'); // Becomes /exam/submit
        Route::get('/result/{examSession}', [McqController::class, 'result'])->name('result');
        
        // Progress & Reviews
        Route::post('/mark-review', [McqController::class, 'markForReview'])->name('mark-review');
        Route::post('/set-review-mode', function (Request $request) {
            session(['review_mode' => $request->input('review_mode', false)]);
            return response()->json(['success' => true]);
        })->name('set-review-mode'); // ✅ FIXED: Stripped duplicate 'exam.' naming
    });

}); // ✅ FIXED: Closes the global Route::middleware('auth') group