@extends('layouts.app')

@section('title', 'Exam Results')

@section('content')
<div class="py-12 bg-light">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- RESULT HEADER --}}
        <div class="card mb-4 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-4 fw-bold mb-2">
                            @if ($examSession->percentage >= 80)
                                🎉 Excellent Performance!
                            @elseif ($examSession->percentage >= 60)
                                ✅ Good Job!
                            @elseif ($examSession->percentage >= 50)
                                👍 You Passed!
                            @else
                                📚 Keep Practicing!
                            @endif
                        </h1>
                        <p class="fs-5 mb-0">Your exam has been successfully submitted and evaluated.</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div style="font-size: 4rem; font-weight: bold;">{{ round($examSession->percentage, 1) }}%</div>
                        <div style="font-size: 1.3rem; color: rgba(255,255,255,0.9);">{{ $examSession->correct_answers }}/{{ $examSession->total_questions }} Correct</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATISTICS ROW --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <div style="font-size: 2.5rem; color: #28a745; font-weight: bold;">
                            {{ $examSession->correct_answers }}
                        </div>
                        <div class="text-muted fw-bold">Correct Answers</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <div style="font-size: 2.5rem; color: #dc3545; font-weight: bold;">
                            {{ $examSession->wrong_answers }}
                        </div>
                        <div class="text-muted fw-bold">Incorrect Answers</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <div style="font-size: 2.5rem; color: #9CA3AF; font-weight: bold;">
                            {{ $examSession->unanswered_count ?? ($examSession->total_questions - ($examSession->correct_answers + $examSession->wrong_answers)) }}
                        </div>
                        <div class="text-muted fw-bold">Unanswered</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <div style="font-size: 2.5rem; color: #0d6efd; font-weight: bold;">
                            @php
                                if ($examSession->finished_at && $examSession->started_at) {
                                    $totalSeconds = \Carbon\Carbon::parse($examSession->finished_at)->timestamp - \Carbon\Carbon::parse($examSession->started_at)->timestamp;
                                    $minutes = floor($totalSeconds / 60);
                                    $seconds = $totalSeconds % 60;
                                    echo $minutes . 'm ' . $seconds . 's';
                                } else {
                                    echo 'N/A';
                                }
                            @endphp
                        </div>
                        <div class="text-muted fw-bold">Time Taken</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRADE & PERFORMANCE CARDS --}}
        @php
            if ($examSession->percentage >= 100) {
                $grade = ['grade' => 'A+', 'color' => '#198754', 'emoji' => '🏆', 'remarks' => 'Outstanding Performance'];
            } elseif ($examSession->percentage >= 90) {
                $grade = ['grade' => 'A', 'color' => '#198754', 'emoji' => '🎉', 'remarks' => 'Excellent Performance'];
            } elseif ($examSession->percentage >= 80) {
                $grade = ['grade' => 'B+', 'color' => '#0d6efd', 'emoji' => '👏', 'remarks' => 'Very Good'];
            } elseif ($examSession->percentage >= 70) {
                $grade = ['grade' => 'B', 'color' => '#0d6efd', 'emoji' => '👍', 'remarks' => 'Good Job'];
            } elseif ($examSession->percentage >= 60) {
                $grade = ['grade' => 'C', 'color' => '#fd7e14', 'emoji' => '🙂', 'remarks' => 'Satisfactory'];
            } elseif ($examSession->percentage >= 50) {
                $grade = ['grade' => 'D', 'color' => '#ffc107', 'emoji' => '📚', 'remarks' => 'Pass'];
            } else {
                $grade = ['grade' => 'F', 'color' => '#dc3545', 'emoji' => '❌', 'remarks' => 'Need Improvement'];
            }
        @endphp

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-3">Your Grade</h6>
                        <div style="font-size: 4rem; font-weight: bold; color: {{ $grade['color'] }};">
                            {{ $grade['grade'] }}
                        </div>
                        <p class="mt-3 mb-0 fw-bold">
                            <span style="color: {{ $grade['color'] }};">
                                {{ $grade['emoji'] }} {{ $grade['remarks'] }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-4">📊 Performance by Difficulty</h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <div style="font-size: 1.8rem; font-weight: bold; color: #28a745;">
                                    {{ $difficultyBreakdown['easy']['correct'] }}/{{ $difficultyBreakdown['easy']['total'] }}
                                </div>
                                <small class="text-success fw-bold">Easy</small>
                                <div class="text-muted text-sm">{{ $difficultyBreakdown['easy']['percentage'] }}%</div>
                            </div>

                            <div class="col-4">
                                <div style="font-size: 1.8rem; font-weight: bold; color: #ffc107;">
                                    {{ $difficultyBreakdown['medium']['correct'] }}/{{ $difficultyBreakdown['medium']['total'] }}
                                </div>
                                <small class="text-warning fw-bold">Medium</small>
                                <div class="text-muted text-sm">{{ $difficultyBreakdown['medium']['percentage'] }}%</div>
                            </div>

                            <div class="col-4">
                                <div style="font-size: 1.8rem; font-weight: bold; color: #dc3545;">
                                    {{ $difficultyBreakdown['hard']['correct'] }}/{{ $difficultyBreakdown['hard']['total'] }}
                                </div>
                                <small class="text-danger fw-bold">Hard</small>
                                <div class="text-muted text-sm">{{ $difficultyBreakdown['hard']['percentage'] }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CHARTS SECTION --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">📈 Score Distribution</h6>
                    </div>
                    <div class="card-body text-center">
                        <canvas id="scoreChart" height="180"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">⭐ Difficulty-wise Performance</h6>
                    </div>
                    <div class="card-body text-center">
                        <canvas id="difficultyChart" height="180"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- COMPLETE ANSWER REVIEW --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📋 Complete Answer Review Sheet</h5>
            </div>
            <div class="card-body">
                @forelse ($answerLogs as $index => $log)
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Question {{ $index + 1 }}</h6>
                                <small class="text-muted">
                                    Difficulty: 
                                    @if ($log->mcq->difficulty === 'easy')
                                        <span class="badge bg-success">Easy</span>
                                    @elseif ($log->mcq->difficulty === 'medium')
                                        <span class="badge bg-warning text-dark">Medium</span>
                                    @else
                                        <span class="badge bg-danger">Hard</span>
                                    @endif
                                </small>
                            </div>
                            {{-- REMOVED UNANSWERED/CORRECT TEXT BADGES FROM RIGHT CORNER --}}
                        </div>

                        <div class="card-body">
                            <p class="fw-bold fs-5 mb-4">{{ $log->mcq->question }}</p>

                            <div class="options-review">
                                @php
                                    $options = [
                                        'A' => $log->mcq->option_a,
                                        'B' => $log->mcq->option_b,
                                        'C' => $log->mcq->option_c,
                                        'D' => $log->mcq->option_d,
                                    ];
                                @endphp

                                @foreach ($options as $letter => $optionText)
                                    @php
                                        // Cast everything cleanly to strings to verify option keys
                                        $studentSelected = !is_null($log->selected_answer) && (string)$log->selected_answer === (string)$letter;
                                        $isCorrectOption = !is_null($log->correct_answer) && (string)$log->correct_answer === (string)$letter;
                                        
                                        if ($isCorrectOption) {
                                            // Correct choice gets a clear green ring
                                            $bgClass = 'border-2 border-success bg-success bg-opacity-5';
                                            $circleColor = '#28a745';
                                            $circleTextColor = '#ffffff';
                                            $circleIcon = '✓';
                                        } elseif ($studentSelected && !$isCorrectOption) {
                                            // Wrong option clicked by user turns bold red
                                            $bgClass = 'border-2 border-danger bg-danger bg-opacity-5';
                                            $circleColor = '#dc3545';
                                            $circleTextColor = '#ffffff';
                                            $circleIcon = '✗';
                                        } else {
                                            // Unanswered / normal choices rest cleanly uncolored
                                            $bgClass = 'border border-secondary border-opacity-25 bg-white';
                                            $circleColor = '#f8f9fa';
                                            $circleTextColor = '#495057';
                                            $circleIcon = $letter;
                                        }
                                    @endphp

                                    <div class="p-3 mb-3 rounded d-flex align-items-center {{ $bgClass }}">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; margin-right: 15px; flex-shrink: 0; background: {{ $circleColor }}; color: {{ $circleTextColor }}; border: 1px solid #dee2e6;">
                                            {{ $circleIcon }}
                                        </div>

                                        <div class="flex-grow-1">
                                            <strong style="font-size: 1.1rem; color: #333;">{{ $letter }}.</strong> {{ $optionText }}
                                        </div>
                                    </div>
                                @endforeach
                            {{-- EXPLANATION --}}
                            @if ($log->mcq->explanation)
                                <div class="alert alert-info mt-4 mb-0">
                                    <strong><i class="fas fa-lightbulb"></i> Explanation:</strong>
                                    <p class="mb-0 mt-2">{{ $log->mcq->explanation }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> No results found.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="mt-4 d-flex gap-2 justify-content-center flex-wrap">
            <a href="{{ route('student.dashboard') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-home"></i> Back to Dashboard
            </a>
            <a href="{{ route('exam.select-subject') }}" class="btn btn-success btn-lg">
                <i class="fas fa-redo"></i> Take Another Exam
            </a>
        </div>

    </div>
</div>

<style>
    .options-review .p-3 {
        transition: all 0.3s ease;
    }
    .options-review .p-3:hover {
        transform: translateX(5px);
    }
    .text-sm {
        font-size: 0.85rem;
    }
    .bg-opacity-5 {
        --bs-bg-opacity: 0.05;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scoreCtx = document.getElementById('scoreChart').getContext('2d');
        new Chart(scoreCtx, {
            type: 'doughnut',
            data: {
                labels: ['Correct', 'Wrong', 'Unanswered'],
                datasets: [{
                    data: [
                        {{ $examSession->correct_answers }}, 
                        {{ $examSession->wrong_answers }}, 
                        {{ $examSession->unanswered_count }}
                    ],
                    backgroundColor: ['#28a745', '#dc3545', '#9CA3AF'],
                    borderColor: ['#ffffff', '#ffffff', '#ffffff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        const diffCtx = document.getElementById('difficultyChart').getContext('2d');
        new Chart(diffCtx, {
            type: 'bar',
            data: {
                labels: ['Easy', 'Medium', 'Hard'],
                datasets: [{
                    label: 'Accuracy Rate (%)',
                    data: [
                        {{ $difficultyBreakdown['easy']['percentage'] }}, 
                        {{ $difficultyBreakdown['medium']['percentage'] }}, 
                        {{ $difficultyBreakdown['hard']['percentage'] }}
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });
    });
</script>
@endsection