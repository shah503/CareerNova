@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
                <div>
                    @if ($systemActive)
                        <span class="badge bg-success">System Active</span>
                    @else
                        <span class="badge bg-danger">System Inactive</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Total Students</h6>
                            <h3 class="text-primary">{{ $stats['total_students'] }}</h3>
                        </div>
                        <i class="fas fa-users fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Total Teachers</h6>
                            <h3 class="text-info">{{ $stats['total_teachers'] }}</h3>
                        </div>
                        <i class="fas fa-chalkboard-user fa-3x text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Total Subjects</h6>
                            <h3 class="text-success">{{ $stats['total_subjects'] }}</h3>
                        </div>
                        <i class="fas fa-book fa-3x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Total MCQs</h6>
                            <h3 class="text-warning">{{ $stats['total_mcqs'] }}</h3>
                        </div>
                        <i class="fas fa-question-circle fa-3x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Stats -->
        <div class="col-md-8">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">Tests Conducted</h6>
                            <h2 class="text-primary">{{ $stats['total_tests_conducted'] }}</h2>
                            <p class="small text-muted mt-2">Total exams completed by all users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">Average Score</h6>
                            <h2 class="text-success">{{ round($stats['avg_score'] ?? 0, 1) }}%</h2>
                            <p class="small text-muted mt-2">Overall platform average</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Reviews -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock"></i> Pending MCQ Reviews
                        <span class="badge bg-danger float-end">{{ $stats['pending_reviews'] }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if ($stats['pending_reviews'] > 0)
                        <p class="mb-3">You have {{ $stats['pending_reviews'] }} MCQs pending review from teachers.</p>
                        <a href="/admin/mcqs/pending" class="btn btn-warning">
                            <i class="fas fa-eye"></i> Review MCQs
                        </a>
                    @else
                        <p class="text-muted">All MCQs are reviewed and approved!</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="/admin/users" class="list-group-item list-group-item-action">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                        <a href="/admin/subjects" class="list-group-item list-group-item-action">
                            <i class="fas fa-book"></i> Manage Subjects
                        </a>
                        <a href="/admin/mcqs/pending" class="list-group-item list-group-item-action">
                            <i class="fas fa-check-circle"></i> Review MCQs
                        </a>
                        <a href="/admin/analytics" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-line"></i> View Analytics
                        </a>
                        <a href="/admin/settings" class="list-group-item list-group-item-action">
                            <i class="fas fa-sliders-h"></i> Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-heartbeat"></i> System Status</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">
                        <strong>Status:</strong><br>
                        <span class="badge {{ $systemActive ? 'bg-success' : 'bg-danger' }}">
                            {{ $systemActive ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                    <p class="small mb-0">
                        <a href="/admin/settings" class="btn btn-sm btn-outline-secondary">Configure</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection