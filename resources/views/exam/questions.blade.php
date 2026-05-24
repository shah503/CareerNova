@extends('layouts.app')

@section('title', 'Exam - Questions')

@section('content')
<div class="container-fluid py-4">
    <div class="row h-100">
        <!-- Main Question Area -->
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-pencil"></i> {{ $session->subject->name }} - Exam
                    </h5>
                    <div class="timer" id="timer">
                        <i class="fas fa-clock"></i> <span id="timeDisplay">00:00</span>
                    </div>
                </div>
                <div class="card-body">
                    <form id="examForm">
                        @csrf
                        
                        <div id="questionsContainer">
                            @foreach ($questions as $index => $question)
                                <div class="question-card mb-4 p-3 border rounded question-{{ $question['id'] }}" 
                                     data-question-id="{{ $question['id'] }}" style="display: {{ $index === 0 ? 'block' : 'none' }}">
                                    
                                    <div class="d-flex justify-content-between mb-3">
                                        <h6 class="fw-bold">
                                            Question {{ $index + 1 }} of {{ count($questions) }}
                                        </h6>
                                        <span class="badge bg-info">{{ $question['order'] + 1 }}</span>
                                    </div>

                                    <p class="fs-6 mb-3">{{ $question['question'] }}</p>

                                    <div class="options">
                                        @foreach ($question['options'] as $option => $text)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input answer-radio" type="radio" 
                                                       name="answer_{{ $question['id'] }}" 
                                                       id="option_{{ $question['id'] }}_{{ $option }}"
                                                       value="{{ $option }}"
                                                       {{ $question['selected_answer'] === $option ? 'checked' : '' }}>
                                                <label class="form-check-label w-100 p-2" 
                                                       for="option_{{ $question['id'] }}_{{ $option }}">
                                                    <strong>{{ $option }}.</strong> {{ $text }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-4 d-flex gap-2">
                                        @if ($index > 0)
                                            <button type="button" class="btn btn-outline-secondary btn-prev">
                                                <i class="fas fa-arrow-left"></i> Previous
                                            </button>
                                        @endif
                                        
                                        @if ($index < count($questions) - 1)
                                            <button type="button" class="btn btn-primary btn-next">
                                                Next <i class="fas fa-arrow-right"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-success btn-submit-exam">
                                                <i class="fas fa-check"></i> Submit Exam
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Sidebar - Question Navigator -->
        <div class="col-md-3">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-list"></i> Questions</h6>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    <div class="question-grid mb-3">
                        @foreach ($questions as $index => $question)
                            <button type="button" class="btn btn-sm btn-outline-primary question-btn question-btn-{{ $question['id'] }}" 
                                    data-question-index="{{ $index }}" data-question-id="{{ $question['id'] }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <hr>

                    <!-- Progress -->
                    <div class="mb-3">
                        <h6 class="small">Progress</h6>
                        <div class="progress">
                            <div class="progress-bar bg-success" id="progressBar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted">
                            <span id="answeredCount">0</span> / {{ count($questions) }} answered
                        </small>
                    </div>

                    <!-- Legend -->
                    <div class="small">
                        <div class="mb-2">
                            <span class="badge bg-success me-2"></span> Answered
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-warning me-2"></span> Skipped
                        </div>
                        <div>
                            <span class="badge bg-danger me-2"></span> Current
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .question-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }
    .question-grid .btn {
        padding: 8px;
        font-size: 12px;
    }
    .timer {
        font-size: 18px;
        font-weight: bold;
    }
    .timer.warning {
        color: #ffc107;
    }
    .timer.danger {
        color: #dc3545;
    }
</style>

@push('scripts')
<script>
    let currentQuestion = 0;
    const questions = @json($questions);
    const totalQuestions = questions.length;
    let timeRemaining = {{ $timeRemaining }};

    // Timer
    function updateTimer() {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        document.getElementById('timeDisplay').textContent = 
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

        const timerElement = document.getElementById('timer');
        if (timeRemaining <= 60) {
            timerElement.classList.add('danger');
        } else if (timeRemaining <= 300) {
            timerElement.classList.add('warning');
        }

        if (timeRemaining <= 0) {
            autoSubmitExam();
            return;
        }

        timeRemaining--;
        setTimeout(updateTimer, 1000);
    }

    // Show question
    function showQuestion(index) {
        document.querySelectorAll('.question-card').forEach(card => card.style.display = 'none');
        document.querySelector('.question-' + questions[index].id).style.display = 'block';
        currentQuestion = index;
        updateNavigator();
    }

    // Update navigator
    function updateNavigator() {
        document.querySelectorAll('.question-btn').forEach(btn => btn.classList.remove('btn-danger', 'btn-success'));
        
        let answeredCount = 0;
        questions.forEach((q, i) => {
            const btn = document.querySelector('.question-btn-' + q.id);
            const answer = document.querySelector(`input[name="answer_${q.id}"]:checked`);
            
            if (answer) {
                btn.classList.add('btn-success');
                answeredCount++;
            } else {
                btn.classList.add('btn-warning');
            }
        });

        document.querySelector('.question-btn-' + questions[currentQuestion].id).classList.remove('btn-success', 'btn-warning');
        document.querySelector('.question-btn-' + questions[currentQuestion].id).classList.add('btn-danger');

        document.getElementById('answeredCount').textContent = answeredCount;
        document.getElementById('progressBar').style.width = (answeredCount / totalQuestions) * 100 + '%';
    }

    // Save answer on change
    document.querySelectorAll('.answer-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const questionId = this.name.replace('answer_', '');
            const sessionId = {{ $session->id }};
            const answer = this.value;

            fetch(`/exam/session/${sessionId}/answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    mcq_id: questionId,
                    selected_answer: answer
                })
            }).then(response => response.json())
              .then(data => {
                  console.log('Answer saved:', data);
                  updateNavigator();
              });
        });
    });

    // Navigation
    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentQuestion < totalQuestions - 1) {
                showQuestion(currentQuestion + 1);
            }
        });
    });

    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentQuestion > 0) {
                showQuestion(currentQuestion - 1);
            }
        });
    });

    document.querySelectorAll('.question-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const index = parseInt(btn.dataset.questionIndex);
            showQuestion(index);
        });
    });

    // Submit exam
    document.querySelector('.btn-submit-exam').addEventListener('click', () => {
        if (confirm('Are you sure you want to submit the exam?')) {
            const sessionId = {{ $session->id }};
            window.location.href = `/exam/session/${sessionId}/submit`;
        }
    });

    function autoSubmitExam() {
        alert('Time is up! Your exam will be auto-submitted.');
        const sessionId = {{ $session->id }};
        window.location.href = `/exam/session/${sessionId}/auto-submit`;
    }

    // Initialize
    showQuestion(0);
    updateTimer();
</script>
@endpush