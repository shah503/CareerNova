@extends('layouts.app')

@section('content')

<!-- HERO SECTION -->
<div style="background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); color: white; padding: 100px 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 48px; font-weight: bold; margin-bottom: 20px;">
            🎓 Welcome to CareerNova
        </h1>
        <p style="font-size: 20px; margin-bottom: 30px;">
            Professional MCQ Testing Platform for MDCAT, NTS, ETEA & Competitive Exams
        </p>

        @if(auth()->check())
            <div>
                @if(auth()->user()->isStudent())
                    <a href="{{ route('student.dashboard') }}" class="btn btn-light btn-lg me-2">
                        📊 Go to Dashboard
                    </a>
                @elseif(auth()->user()->isTeacher())
                    <a href="{{ route('teacher.dashboard') }}" class="btn btn-light btn-lg me-2">
                        👨‍🏫 Teacher Dashboard
                    </a>
                @elseif(auth()->user()->isParent())
                    <a href="{{ route('parent.dashboard') }}" class="btn btn-light btn-lg me-2">
                        👨‍👩‍👧 Parent Dashboard
                    </a>
                @elseif(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-lg me-2">
                        ⚙️ Admin Dashboard
                    </a>
                @endif
            </div>
        @else
            <div>
                <a href="{{ route('register') }}" class="btn btn-light btn-lg me-2">
                    📝 Register
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                    🔐 Login
                </a>
            </div>
        @endif
    </div>
</div>

<!-- FEATURES SECTION -->
<div class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">✨ Why Choose CareerNova?</h2>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div style="font-size: 48px; margin-bottom: 20px;">📝</div>
                        <h5 class="card-title">Real Exam Experience</h5>
                        <p class="card-text">
                            Realistic MCQ interface matching MDCAT, NTS & ETEA formats with timed tests & auto-save answers.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div style="font-size: 48px; margin-bottom: 20px;">📊</div>
                        <h5 class="card-title">Detailed Analytics</h5>
                        <p class="card-text">
                            Track your performance with subject-wise analysis, weak topics, score trends & improvement suggestions.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div style="font-size: 48px; margin-bottom: 20px;">👨‍🏫</div>
                        <h5 class="card-title">Teacher Control</h5>
                        <p class="card-text">
                            Create MCQs, manage classes, track student progress & generate performance reports instantly.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div style="font-size: 48px; margin-bottom: 20px;">👨‍👩‍👧</div>
                        <h5 class="card-title">Parent Dashboard</h5>
                        <p class="card-text">
                            Monitor your child's exam progress, performance analytics, weak topics & improvement areas.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div style="font-size: 48px; margin-bottom: 20px;">🔒</div>
                        <h5 class="card-title">Anti-Cheating</h5>
                        <p class="card-text">
                            Session locking, auto-save answers & question randomization prevent cheating & data loss.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div style="font-size: 48px; margin-bottom: 20px;">⚡</div>
                        <h5 class="card-title">Fast & Reliable</h5>
                        <p class="card-text">
                            Lightning-fast test loading, 1-minute auto-save intervals & secure cloud infrastructure.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- STUDENT VS TEACHER -->
<div class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">✅ How It Works</h2>

        <div class="row text-center">
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-left-primary">
                    <div class="card-body">
                        <div style="font-size: 36px; margin-bottom: 20px;">👨‍🎓</div>
                        <h5 class="card-title">For Students</h5>
                        <ul class="list-unstyled" style="text-align: left;">
                            <li>✓ Register as a student</li>
                            <li>✓ Take unlimited practice tests</li>
                            <li>✓ Auto-save answers every minute</li>
                            <li>✓ View instantly scored results</li>
                            <li>✓ Complete performance analytics</li>
                            <li>✓ Leaderboard rankings</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100 border-left-success">
                    <div class="card-body">
                        <div style="font-size: 36px; margin-bottom: 20px;">👨‍🏫</div>
                        <h5 class="card-title">For Teachers</h5>
                        <ul class="list-unstyled" style="text-align: left;">
                            <li>✓ Register as a teacher</li>
                            <li>✓ Create & manage MCQs</li>
                            <li>✓ Track student progress</li>
                            <li>✓ Generate performance reports</li>
                            <li>✓ Class-wise analytics</li>
                            <li>✓ Export reports & statistics</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- STATS -->
<div class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">📈 CareerNova by Numbers</h2>

        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="display-4 text-primary fw-bold">
                    {{ \App\Models\User::where('role', 'student')->count() }}+
                </div>
                <p class="text-muted">Active Students</p>
            </div>

            <div class="col-md-3 mb-4">
                <div class="display-4 text-success fw-bold">
                    {{ \App\Models\Mcq::count() }}+
                </div>
                <p class="text-muted">MCQs Created</p>
            </div>

            <div class="col-md-3 mb-4">
                <div class="display-4 text-info fw-bold">
                    {{ \App\Models\ExamSession::count() }}+
                </div>
                <p class="text-muted">Tests Attempted</p>
            </div>

            <div class="col-md-3 mb-4">
                <div class="display-4 text-warning fw-bold">
                    {{ \App\Models\User::where('role', 'teacher')->count() }}+
                </div>
                <p class="text-muted">Teachers</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA -->
<div style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); color: white; padding: 80px 0; text-align: center;">
    <div class="container">
        <h2 class="mb-4">Ready to Start Your Journey?</h2>
        <p style="font-size: 18px; margin-bottom: 30px;">
            Join thousands of students preparing for MDCAT with CareerNova
        </p>

        @if(auth()->check())
            @if(auth()->user()->isStudent())
                <a href="{{ route('exam.select-subject') }}" class="btn btn-light btn-lg">
                    📝 Take a Test Now
                </a>
            @else
                <a href="javascript:history.back()" class="btn btn-light btn-lg">
                    📊 Go to Dashboard
                </a>
            @endif
        @else
            <a href="{{ route('register') }}" class="btn btn-light btn-lg me-2">
                📝 Register Now
            </a>
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                🔐 Already have an account?
            </a>
        @endif
    </div>
</div>

@endsection