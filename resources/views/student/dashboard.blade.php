@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-chart-line"></i> Your Dashboard</h2>
                <a href="/exam/select-subject" class="btn btn-primary">
                    <i class="fas fa-pencil"></i> Take Exam
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light border-left-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Total Tests</h6>
                            <h3 class="text-primary">{{ $analytics['total_tests'] }}</h3>
                        </div>
                        <i class="fas fa-pencil fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-light border-left-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Correct Answers</h6>
                            <h3 class="text-success">{{ $analytics['total_correct_answers'] }}</h3>
                        </div>
                        <i class="fas fa-check fa-3x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-light border-left-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Average Score</h6>
                            <h3 class="text-info">{{ round($analytics['overall_percentage'], 1) }}%</h3>
                        </div>
                        <i class="fas fa-star fa-3x text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-light border-left-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Your Rank</h6>
                            <h3 class="text-warning">#{{ $userRank ?? 'N/A' }}</h3>
                        </div>
                        <i class="fas fa-trophy fa-3x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Tests -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Tests</h5>
                </div>
                <div class="card-body">
                    @if ($recentSessions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subject</th>
                                        <th>Score</th>
                                        <th>Percentage</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentSessions as $session)
                                        <tr>
                                            <td>{{ $session->subject->name }}</td>
                                            <td>{{ $session->correct_answers }}/{{ $session->total_questions }}</td>
                                            <td>
                                                <span class="badge bg-{{ $session->percentage >= 70 ? 'success' : ($session->percentage >= 50 ? 'warning' : 'danger') }}">
                                                    {{ round($session->percentage, 1) }}%
                                                </span>
                                            </td>
                                            <td>{{ $session->finished_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="/exam/session/{{ $session->id }}/result" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="/student/exam-history" class="btn btn-outline-primary">
                                View All Tests <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No tests taken yet. Start with an exam now!</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Weak Areas -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Weak Areas</h5>
                </div>
                <div class="card-body">
                    @if ($weakAreas->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($weakAreas as $area)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $area->name }}</span>
                                    <span class="badge bg-danger rounded-pill">{{ $area->wrong_count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No weak areas identified yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection