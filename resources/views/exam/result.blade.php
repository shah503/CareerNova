@extends('layouts.app')

@section('title', 'Exam Results')

@section('content')
<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

        {{-- RESULT HEADER --}}
        <div class="card mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="text-4xl font-weight-bold mb-2">
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
                        <p class="mb-0">Your exam has been successfully submitted and evaluated.</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div style="font-size: 3.5rem; font-weight: bold;">{{ $examSession->percentage }}%</div>
                        <div style="font-size: 1.2rem;">{{ $examSession->score }}/{{ $examSession->total_questions }} Correct</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATISTICS ROW --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div style="font-size: 2.5rem; color: #28a745;">{{ $examSession->correct_answers }}</div>
                        <div class="text-muted">Correct Answers</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div style="font-size: 2.5rem; color: #dc3545;">{{ $examSession->wrong_answers }}</div>
                        <div class="text-muted">Incorrect Answers</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div style="font-size: 2.5rem; color: #ffc107;">{{ $examSession->unanswered_count ?? 0 }}</div>
                        <div class="text-muted">Unanswered</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div style="font-size: 2.5rem; color: #0d6efd;">
                            @php
                                $minutes = floor(($examSession->finished_at->timestamp - $examSession->started_at->timestamp) / 60);
                                $seconds = ($examSession->finished_at->timestamp - $examSession->started_at->timestamp) % 60;
                                echo $minutes . 'm ' . $seconds . 's';
                            @endphp
                        </div>
                        <div class="text-muted">Time Taken</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RANKING INFO --}}
        @if ($userRank !== 'N/A')
            <div class="alert alert-info mb-4">
                <strong><i class="fas fa-trophy"></i> Your Ranking:</strong> You are currently ranked #{{ $userRank }} on the leaderboard!
            </div>
        @endif

        {{-- ANSWER REVIEW --}}
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">📋 Answer Review</h5>
            </div>
            <div class="card-body">
                @foreach ($results as $index => $result)
                    <div class="card mb-3 {{ $result['is_correct'] ? 'border-success' : 'border-danger' }}">
                        <div class="card-header {{ $result['is_correct'] ? 'bg-success' : 'bg-danger' }} text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Question {{ $index + 1 }}</h6>
                                <span class="badge {{ $result['is_correct'] ? 'bg-light' : 'bg-danger' }} text-dark">
                                    {{ $result['is_correct'] ? '✓ CORRECT' : '✗ INCORRECT' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="fw-bold mb-4">{{ $result['question'] }}</p>

                            <div class="options-review">
                                @foreach (['A', 'B', 'C', 'D'] as $option)
                                    @php
                                        $optionText = $result['option_' . strtolower($option)];
                                        $isStudentAnswer = $result['student_answer'] === $option;
                                        $isCorrectAnswer = $result['correct_answer'] === $option;
                                    @endphp
                                    <div class="p-3 mb-2 rounded {{ 
                                        $isCorrectAnswer ? 'bg-success bg-opacity-10 border border-success' : 
                                        ($isStudentAnswer ? 'bg-danger bg-opacity-10 border border-danger' : 'bg-light')
                                    }}">
                                        <strong class="text-primary">{{ $option }}.</strong> {{ $optionText }}
                                        
                                        @if ($isCorrectAnswer)
                                            <span class="badge bg-success float-end">✓ Correct Answer</span>
                                        @endif
                                        @if ($isStudentAnswer && !$isCorrectAnswer)
                                            <span class="badge bg-danger float-end">✗ Your Answer</span>
                                        @endif
                                        @if ($isStudentAnswer && $isCorrectAnswer)
                                            <span class="badge bg-success float-end">✓ Your Answer</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @if ($result['explanation'])
                                <div class="alert alert-info small mt-3 mb-0">
                                    <strong><i class="fas fa-book"></i> Explanation:</strong>
                                    <p class="mb-0">{{ $result['explanation'] }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="mt-4 d-flex gap-2 justify-content-center">
            <a href="{{ route('student.results') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-list"></i> View All Results
            </a>
            <a href="{{ route('exam.select-subject') }}" class="btn btn-success btn-lg">
                <i class="fas fa-redo"></i> Take Another Exam
            </a>
            <a href="{{ route('student.dashboard') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>

    </div>
</div>
@endsection