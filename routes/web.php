<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Exam\ExamSessionController;
use App\Http\Controllers\Exam\ResultController;
use App\Http\Controllers\Import\CsvImportController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ===================== STUDENT ROUTES =====================
Route::middleware(['auth'])->prefix('student')->group(function () {
    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('student.dashboard');
    Route::get('/exam-history', [StudentDashboard::class, 'examHistory'])->name('student.exam-history');
    Route::get('/leaderboard', [StudentDashboard::class, 'leaderboard'])->name('student.leaderboard');
    Route::get('/analytics', [StudentDashboard::class, 'analytics'])->name('student.analytics');
});

// ===================== EXAM ROUTES =====================
Route::middleware(['auth'])->prefix('exam')->group(function () {
    // Select subject and start exam
    Route::get('/select-subject', [ExamSessionController::class, 'selectSubject'])->name('exam.select-subject');
    Route::post('/create', [ExamSessionController::class, 'create'])->name('exam.create');
    
    // Exam session routes
    Route::get('/session/{session}/start', [ExamSessionController::class, 'start'])->name('exam.start');
    Route::get('/session/{session}/questions', [ExamSessionController::class, 'questions'])->name('exam.questions');
    Route::post('/session/{session}/answer', [ExamSessionController::class, 'submitAnswer'])->name('exam.submit-answer');
    Route::post('/session/{session}/submit', [ExamSessionController::class, 'submit'])->name('exam.submit');
    Route::post('/session/{session}/auto-submit', [ExamSessionController::class, 'autoSubmit'])->name('exam.auto-submit');
    
    // AJAX routes for exam
    Route::get('/session/{session}/time-remaining', [ExamSessionController::class, 'getTimeRemaining']);
    Route::get('/session/{session}/progress', [ExamSessionController::class, 'getProgress']);
    
    // Results
    Route::get('/session/{session}/result', [ResultController::class, 'show'])->name('exam.result');
    Route::get('/session/{session}/answer/{mcqId}', [ResultController::class, 'getAnswerDetail']);
    Route::get('/session/{session}/download-pdf', [ResultController::class, 'downloadPdf'])->name('exam.download-pdf');
});

// ===================== IMPORT ROUTES =====================
Route::middleware(['auth'])->prefix('import')->group(function () {
    Route::get('/csv', [CsvImportController::class, 'showForm'])->name('import.csv-form');
    Route::post('/csv', [CsvImportController::class, 'import'])->name('import.csv');
    Route::get('/csv-template', [CsvImportController::class, 'downloadTemplate'])->name('import.csv-template');
});

// ===================== ADMIN ROUTES =====================
Route::middleware(['auth', 'admin.check'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'listUsers'])->name('admin.users.list');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    // MCQ Management
    Route::get('/mcqs/pending', [AdminController::class, 'pendingMcqs'])->name('admin.mcqs.pending');
    Route::post('/mcqs/{mcq}/approve', [AdminController::class, 'approveMcq'])->name('admin.mcqs.approve');
    Route::post('/mcqs/{mcq}/reject', [AdminController::class, 'rejectMcq'])->name('admin.mcqs.reject');
    
    // Subjects
    Route::resource('subjects', SubjectController::class, ['names' => [
        'index' => 'admin.subjects.index',
        'create' => 'admin.subjects.create',
        'store' => 'admin.subjects.store',
        'edit' => 'admin.subjects.edit',
        'update' => 'admin.subjects.update',
        'destroy' => 'admin.subjects.destroy',
    ]]);
    Route::post('/subjects/{subject}/toggle', [SubjectController::class, 'toggle'])->name('admin.subjects.toggle');
    
    // Analytics
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
    
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings/general', [SettingController::class, 'updateGeneral'])->name('admin.settings.general');
    Route::post('/settings/payment', [SettingController::class, 'updatePayment'])->name('admin.settings.payment');
    Route::post('/settings/feature/toggle', [SettingController::class, 'toggleFeature'])->name('admin.settings.toggle-feature');
    Route::post('/settings/system/toggle', [SettingController::class, 'toggleSystem'])->name('admin.settings.toggle-system');
    
    // System Control
    Route::post('/system/toggle', [AdminController::class, 'toggleSystem'])->name('admin.system.toggle');
    Route::post('/system/feature/toggle', [AdminController::class, 'toggleFeature'])->name('admin.system.toggle-feature');
});
// ===================== API ROUTES (if needed) =====================
Route::middleware(['auth'])->prefix('api')->group(function () {
    // Exam API endpoints
    Route::post('/exam/{session}/answer', [ExamSessionController::class, 'submitAnswer']);
    Route::get('/exam/{session}/time', [ExamSessionController::class, 'getTimeRemaining']);
    Route::get('/exam/{session}/progress', [ExamSessionController::class, 'getProgress']);
    Route::get('/exam/{session}/result/{mcqId}', [ResultController::class, 'getAnswerDetail']);
});