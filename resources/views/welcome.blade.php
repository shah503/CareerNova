@extends('layouts.app')

@section('content')

<!-- HERO SECTION -->
<div class="hero-section" style="background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); color: white; padding: 100px 0; text-align: center;">
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
                <a href="{{ route('login') }}" class="btn btn-light btn-lg me-2">
                    🔐 Login
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                    📝 Register
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

            <!-- FEATURE 1 -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div style="font-size: 48px; margin-bottom: 20px;">📝</div>
                        <h5 class="card-title">Real Exam Experience</h5>
                        <p class="card-text">
                            Realistic MCQ interface matching MDCAT, NTS & ETEA formats with timed tests and auto-submission.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FEATURE 2 -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div style="font-size: 48px; margin-bottom: 20px;">📊</div>
                        <h5 class="card-title">Detailed Analytics</h5>
                        <p class="card-text">
                            Track your performance with subject-wise analysis, weak topic identification & progress tracking.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FEATURE 3 -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div style="font-size: 48px; margin-bottom: 20px;">🏆</div>
                        <h5 class="card-title">Gamification</h5>
                        <p class="card-text">
                            Earn points, unlock badges & compete on leaderboards. Learning becomes fun and motivating.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FEATURE 4 -->
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

            <!-- FEATURE 5 -->
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

            <!-- FEATURE 6 -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div style="font-size: 48px; margin-bottom: 20px;">⚡</div>
                        <h5 class="card-title">Fast & Reliable</h5>
                        <p class="card-text">
                            Lightning-fast test loading, 1-minute auto-save intervals & reliable cloud infrastructure.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- HOW IT WORKS SECTION -->
<div class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">🚀 How It Works</h2>

        <div class="row align-items-center">

            <!-- FOR STUDENTS -->
            <div class="col-md-6 mb-4">
                <div class="card border-primary border-3">
                    <div class="card-body">
                        <h5 class="card-title text-primary">🧑‍🎓 For Students</h5>
                        <ol class="ps-3">
                            <li>Register as a student</li>
                            <li>Select a test or join a class</li>
                            <li>Take the test with real-time timer</li>
                            <li>Review results with detailed explanations</li>
                            <li>Track progress & identify weak topics</li>
                            <li>Compete on leaderboards & earn badges</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- FOR TEACHERS -->
            <div class="col-md-6 mb-4">
                <div class="card border-success border-3">
                    <div class="card-body">
                        <h5 class="card-title text-success">👨‍🏫 For Teachers</h5>
                        <ol class="ps-3">
                            <li>Register as a teacher</li>
                            <li>Create MCQs with explanations</li>
                            <li>Create classrooms & add students</li>
                            <li>Assign tests to classes</li>
                            <li>Track student performance</li>
                            <li>Export reports & analytics</li>
                        </ol>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- STATS SECTION -->
<div class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">📈 CareerNova by Numbers</h2>

        <div class="row text-center">

            <div class="col-md-3 mb-4">
                <div class="display-4 text-primary fw-bold">
                    5+
                </div>
                <p class="text-muted">Active Students</p>
            </div>

            <div class="col-md-3 mb-4">
                <div class="display-4 text-success fw-bold">
                    13+
                </div>
                <p class="text-muted">MCQs Created</p>
            </div>

            <div class="col-md-3 mb-4">
                <div class="display-4 text-info fw-bold">
                    0+
                </div>
                <p class="text-muted">Tests Attempted</p>
            </div>

            <div class="col-md-3 mb-4">
                <div class="display-4 text-warning fw-bold">
                    1+
                </div>
                <p class="text-muted">Teachers</p>
            </div>

        </div>
    </div>
</div>

<!-- CTA SECTION -->
<div style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); color: white; padding: 80px 0; text-align: center;">
    <div class="container">
        <h2 class="mb-4">Ready to Start Your Journey?</h2>
        <p style="font-size: 18px; margin-bottom: 30px;">
            Join thousands of students preparing for MDCAT with CareerNova
        </p>

        @if(auth()->check())
            @if(auth()->user()->isStudent())
                <a href="{{ route('mcqs.index') }}" class="btn btn-light btn-lg">
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
                🔐 Login
            </a>
        @endif
    </div>
</div>

<!-- TESTIMONIALS SECTION -->
<div class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">💬 What Students Say</h2>

        <div class="row">

            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <span style="color: #ffc107;">★★★★★</span>
                        </div>
                        <p class="card-text">
                            "CareerNova helped me improve my MDCAT score by 30%. The analytics feature is amazing!"
                        </p>
                        <h6 class="mt-3">- Ahmed Khan</h6>
                        <small class="text-muted">MDCAT 2024</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <span style="color: #ffc107;">★★★★★</span>
                        </div>
                        <p class="card-text">
                            "The realistic exam interface and leaderboard kept me motivated throughout my preparation."
                        </p>
                        <h6 class="mt-3">- Fatima Ali</h6>
                        <small class="text-muted">MDCAT 2024</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <span style="color: #ffc107;">★★★★★</span>
                        </div>
                        <p class="card-text">
                            "Best platform for teachers to manage student performance. Worth every penny!"
                        </p>
                        <h6 class="mt-3">- Dr. Hassan</h6>
                        <small class="text-muted">Teacher</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@
