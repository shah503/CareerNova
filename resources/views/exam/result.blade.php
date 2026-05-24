@extends('layouts.app')

@section('title', 'Exam Results')

@section('content')
<div class="container py-5">
    <!-- Result Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-center py-5">
                    <h2 class="mb-3">{{ $session->subject->name }} - Exam Results</h2>
                    <div class="row">
                        <div class="col-md-3">
                            <h4>{{ $session->score }}/{{ $session->total_questions }}</h4>
                            <p class="small">Score</p>
                        </div>
                        <div class="col-md-3">
                            <h4>{{ $session->percentage }}%</h4>
                            <p class="small">Percentage</p>
                        </div>
                        <div class="col-md-3">
                            <h4>{{ $grade['grade'] }}</h4>
                            <p class="small">Grade</p>
                        </div>
                        <div class="col-md-3">
                            <h4>{{ $session->finished_at->diffForHumans() }}</h4>
                            <p class="small">Completed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Results -->
        <div class="col-md-8">
            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check text-success fa-3x mb-3"></i>
                            <h4 class="text-success">{{ $session->correct_answers }}</h4>
                            <p class="text-muted small">Correct Answers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-times text-danger fa-3x mb-3"></i>
                            <h4 class="text-danger">{{ $session->wrong_answers + $session->unanswered }}</h4>
                            <p class="text-muted small">Wrong/Unanswered</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Difficulty Breakdown -->
            @if (count($scoreByDifficulty) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Performance by Difficulty</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($scoreByDifficulty as $difficulty => $scores)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold text-capitalize">{{ $difficulty }}</span>
                                    <span class="text-muted">{{ $scores['correct'] }}/{{ $scores['total'] }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar {{ $scores['percentage'] >= 70 ? 'bg-success' : ($scores['percentage'] >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                         style="width: {{ $scores['percentage'] }}%">
                                        {{ round($scores['percentage']) }}%
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Answer Review -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list-check"></i> Answer Review</h5>
                </div>
                <div class="card-body">
                    @foreach ($summary['results'] as $index => $result)
                        <div class="border-bottom pb-4 mb-4" style="{{ $index === count($summary['results']) - 1 ? 'border-bottom: none;' : '' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold">Question {{ $index + 1 }}</h6>
                                <span class="badge {{ $result['is_correct'] ? 'bg-success' : 'bg-danger' }}">
                                    {{ $result['is_correct'] ? '✓ Correct' : '✗ Wrong' }}
                                </span>
                            </div>

                            <p class="mb-3">{{ $result['question'] }}</p>

                            <div class="alert {{ $result['is_correct'] ? 'alert-success' : 'alert-danger' }} py-2 mb-3">
                                <small>
                                    <strong>Your Answer:</strong> 
                                    {{ $result['selected_answer'] ?? 'Not answered' }}
                                </small>
                            </div>

                            @if (!$result['is_correct'])
                                <div class="alert alert-info py-2 mb-3">
                                    <small>
                                        <strong>Correct Answer:</strong> {{ $result['correct_answer'] }}
                                    </small>
                                </div>
                            @endif

                            @if ($result['explanation'])
                                <div class="alert alert-light border py-2">
                                    <small>
                                        <strong><i class="fas fa-lightbulb"></i> Explanation:</strong><br>
                                        {{ $result['explanation'] }}
                                    </small>
                                </div>
                            @endif

                            <small class="text-muted">
                                <span class="badge bg-secondary">{{ ucfirst($result['difficulty']) }}</span>
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Grade Card -->
            <div class="card text-center shadow-sm mb-4" style="border-top: 4px solid {{ $grade['color'] === 'success' ? '#28a745' : ($grade['color'] === 'danger' ? '#dc3545' : '#ffc107') }};">
                <div class="card-body py-5">
                    <h1 class="text-{{ $grade['color'] }} mb-3">{{ $grade['grade'] }}</h1>
                    <h5>{{ $grade['remarks'] }}</h5>
                    <p class="text-muted mt-3">Your performance</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-grid gap-2 mb-4">
                <a href="/student/dashboard" class="btn btn-primary">
                    <i class="fas fa-home"></i> Back to Dashboard
                </a>
                <a href="/student/analytics" class="btn btn-outline-primary">
                    <i class="fas fa-chart-line"></i> View Analytics
                </a>
                <a href="/exam/select-subject" class="btn btn-outline-success">
                    <i class="fas fa-pencil"></i> Take Another Exam
                </a>
            </div>

            <!-- Exam Info -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Exam Details</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">
                        <strong>Subject:</strong><br>
                        {{ $session->subject->name }}
                    </p>
                    <p class="small mb-2">
                        <strong>Duration:</strong><br>
                        {{ $session->duration_minutes }} minutes
                    </p>
                    <p class="small mb-2">
                        <strong>Date & Time:</strong><br>
                        {{ $session->finished_at->format('M d, Y h:i A') }}
                    </p>
                    <p class="small mb-0">
                        <strong>Questions:</strong><br>
                        {{ $session->total_questions }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endsection