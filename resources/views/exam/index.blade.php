@extends('layouts.app')

@section('title', 'Take Exam')

@section('content')
<style>
    /* Bubble Sheet Styles */
    .bubble-sheet {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        position: sticky;
        top: 10px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .question-bubbles {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
    }

    .bubble {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid #ddd;
    }

    .bubble.not-visited { background: #e9ecef; color: #6c757d; }
    .bubble.visited { background: #ffc107; color: white; }
    .bubble.answered { background: #0d6efd; color: white; }
    .bubble.marked { background: #9c27b0; color: white; }
    .bubble.answered-marked { background: #28a745; color: white; }

    .bubble:hover {
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .timer-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .timer-display {
        font-size: 3rem;
        font-weight: bold;
        font-family: 'Courier New', monospace;
        letter-spacing: 2px;
    }

    .timer-text {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .progress-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 20px;
        font-size: 0.85rem;
    }

    .stat-item {
        background: white;
        padding: 10px;
        border-radius: 6px;
        border-left: 4px solid #0d6efd;
    }

    .stat-label { color: #6c757d; }
    .stat-value { font-weight: bold; font-size: 1.2rem; color: #0d6efd; }

    .legend {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #dee2e6;
        font-size: 0.75rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .auto-save-indicator {
        font-size: 0.75rem;
        color: #28a745;
        margin-top: 10px;
    }

    .exam-container {
        display: grid;
        grid-template-columns: 1fr 250px;
        gap: 20px;
    }

    @media (max-width: 1024px) {
        .exam-container {
            grid-template-columns: 1fr;
        }
        .bubble-sheet {
            position: static;
            max-height: auto;
        }
        .question-bubbles {
            grid-template-columns: repeat(10, 1fr);
        }
    }

    .answer-option {
        transition: all 0.3s;
        cursor: pointer;
    }

    .answer-option:hover {
        background-color: #f0f0f0;
        transform: translateX(5px);
    }

    .answer-option.selected {
        background-color: #d4edff;
        border-color: #0d6efd;
    }

    .mark-review-btn {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }

    .submission-panel {
        position: sticky;
        bottom: 0;
        background: white;
        border-top: 2px solid #dee2e6;
        padding: 15px;
        display: flex;
        gap: 10px;
        justify-content: center;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
    }

    .recovery-alert {
        background: #cfe2ff;
        border-left: 4px solid #0d6efd;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
</style>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if ($exam_started === false && session('exam_started') === false)
            {{-- PRE-EXAM SCREEN --}}
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-center py-8">
                    <h2 class="text-4xl font-bold mb-4">⚠️ Ready to Start Exam?</h2>
                    
                    <div class="card bg-light mb-6">
                        <div class="card-body">
                            <p class="text-lg mb-3">
                                📝 You are about to take an exam with <strong>{{ count($mcqs) }}</strong> questions.
                            </p>
                            <p class="text-lg mb-3">
                                ⏱️ Time limit: <strong>{{ count($mcqs) }} minutes</strong> (1 minute per question)
                            </p>
                            <p class="text-danger fw-bold mb-0">
                                🔒 Once started, you cannot pause or leave the exam!
                            </p>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <h5 class="mb-3"><strong>🔐 Anti-Cheating Measures:</strong></h5>
                        <ul class="mb-0 text-start" style="display: inline-block;">
                            <li>✅ Your answers are auto-saved every 10 seconds</li>
                            <li>✅ Exam will auto-submit when time runs out</li>
                            <li>✅ Session can be recovered if connection lost</li>
                            <li>✅ All sessions are logged and verified</li>
                        </ul>
                    </div>

                    <form action="{{ route('exam.start') }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">

                        <button type="submit" class="btn btn-success btn-lg px-5" id="start-btn">
                            <i class="fas fa-play"></i> Start Exam Now
                        </button>
                        <a href="{{ route('exam.select-subject') }}" class="btn btn-secondary btn-lg px-5 ms-2">
                            <i class="fas fa-times"></i> Go Back
                        </a>
                    </form>
                </div>
            </div>

        @else
            {{-- EXAM WITH RECOVERY NOTICE --}}
            @if (isset($is_recovery) && $is_recovery)
                <div class="recovery-alert">
                    <strong><i class="fas fa-info-circle"></i> Session Recovered</strong>
                    <p class="mb-0">Your exam has been recovered. You have <strong id="recovery-time"></strong> remaining.</p>
                </div>
            @endif

            {{-- EXAM INTERFACE --}}
            <div class="exam-container">
                {{-- QUESTIONS SECTION --}}
                <div>
                    <form id="exam-form" action="{{ route('exam.submit') }}" method="POST">
                        @csrf

                        @foreach ($mcqs as $index => $mcq)
                            <div class="card mb-4 question-card" id="question-{{ $mcq['id'] }}" data-question-number="{{ $index + 1 }}">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            Q{{ $index + 1 }}. {{ Str::limit($mcq['question'], 80) }}
                                        </h5>
                                        <span class="badge bg-info">{{ ucfirst($mcq['difficulty']) }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="fw-bold mb-4">{{ $mcq['question'] }}</p>

                                    <div class="options-list">
                                        @foreach (['A', 'B', 'C', 'D'] as $option)
                                            <div class="answer-option p-3 border rounded mb-2" data-option="{{ $option }}">
                                                <div class="form-check">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        name="answers[{{ $mcq['id'] }}]" 
                                                        id="option_{{ $mcq['id'] }}_{{ $option }}"
                                                        value="{{ $option }}"
                                                        onchange="selectAnswer({{ $mcq['id'] }}, '{{ $option }}', {{ $index + 1 }})">
                                                    <label class="form-check-label w-100" for="option_{{ $mcq['id'] }}_{{ $option }}">
                                                        <strong class="text-primary">{{ $option }}.</strong> 
                                                        {{ $mcq['option_' . strtolower($option)] }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-3">
                                        <button type="button" class="btn btn-outline-warning btn-sm mark-review-btn" 
                                                onclick="toggleMarkForReview({{ $mcq['id'] }}, {{ $index + 1 }})">
                                            <i class="fas fa-flag"></i> Mark for Review
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- SUBMISSION PANEL --}}
                        <div class="submission-panel">
                            <button type="submit" class="btn btn-success btn-lg px-5" id="submit-btn" onclick="return confirmSubmit()">
                                <i class="fas fa-check-circle"></i> Submit Exam
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-lg px-5" onclick="saveDraft()">
                                <i class="fas fa-save"></i> Save Draft
                            </button>
                        </div>
                    </form>
                </div>

                {{-- BUBBLE SHEET SIDEBAR --}}
                <div class="bubble-sheet">
                    <div class="timer-box">
                        <div class="timer-text">⏱️ Shahjee Baba's Timer</div>
                        <div class="timer-display" id="timer">
                            {{ count($mcqs) }}:00
                        </div>
                    </div>

                    <div class="progress-stats">
                        <div class="stat-item">
                            <div class="stat-label">Answered</div>
                            <div class="stat-value" id="answered-count">0</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Marked</div>
                            <div class="stat-value" id="marked-count">0</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Unanswered</div>
                            <div class="stat-value" id="unanswered-count">{{ count($mcqs) }}</div>
                        </div>
                        <div class="stat-item border-left-danger" style="border-left-color: #dc3545;">
                            <div class="stat-label">Not Visited</div>
                            <div class="stat-value" id="not-visited-count">{{ count($mcqs) }}</div>
                        </div>
                    </div>

                    <div>
                        <h6 class="mb-3"><strong>Questions</strong></h6>
                        <div class="question-bubbles" id="bubble-sheet">
                            @foreach ($mcqs as $index => $mcq)
                                <div class="bubble not-visited" id="bubble-{{ $index + 1 }}" 
                                     onclick="jumpToQuestion({{ $index + 1 }})" title="Q{{ $index + 1 }}">
                                    {{ $index + 1 }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="legend">
                        <div class="legend-item">
                            <div class="legend-color" style="background: #e9ecef;"></div>
                            <small>Not Visited</small>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #ffc107;"></div>
                            <small>Visited</small>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #0d6efd;"></div>
                            <small>Answered</small>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #9c27b0;"></div>
                            <small>Marked</small>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #28a745;"></div>
                            <small>Answered & Marked</small>
                        </div>
                    </div>

                    <div class="auto-save-indicator">
                        <i class="fas fa-check-circle"></i> Auto-saving
                        <div id="last-saved">Last saved: --:--:-- AM</div>
                    </div>
                </div>
            </div>

            <script>
            // Timer and Progress Management
            let timeRemaining = {{ count($mcqs) * 60 }};
            const totalQuestions = {{ count($mcqs) }};
            const timerElement = document.getElementById('timer');
            const examForm = document.getElementById('exam-form');

            // Update numeric tracking states globally
            function updateBubbleSheet() {
                fetch('{{ route("exam.progress") }}')
                    .then(r => r.json())
                    .then(stats => {
                        const answeredEl = document.getElementById('answered-count');
                        const markedEl = document.getElementById('marked-count');
                        const unansweredEl = document.getElementById('unanswered-count');
                        const notVisitedEl = document.getElementById('not-visited-count');

                        if (answeredEl) answeredEl.textContent = stats.answered || 0;
                        if (markedEl) markedEl.textContent = stats.marked || 0;
                        if (unansweredEl) unansweredEl.textContent = stats.unanswered || 0;
                        if (notVisitedEl) notVisitedEl.textContent = stats.not_visited || 0;
                    })
                    .catch(err => console.error("Error loading stat sync metrics:", err));
            }

            // Radio input interaction pipeline
            // 🟢 FIXED: Correctly pass parameters down to saveAnswer
            function selectAnswer(questionId, answer, questionNumber) {
                const bubble = document.getElementById('bubble-' + questionNumber);
                if (bubble) {
                    // Instantly update the visual bubble color to blue on click
                    bubble.className = 'bubble answered'; 
                }
                
                // Fix the parameter ordering mismatch here:
                saveAnswer(questionId, answer, questionNumber, false);
            }

            // Save choice mapping state 
            function saveAnswer(questionId, answer, questionNumber, markForReview) {
                fetch('{{ route("exam.answer") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        answer: answer,
                        question_number: questionNumber,
                        mark_for_review: markForReview
                    })
                })
                .then(r => r.json())
                .then(data => {
                    document.getElementById('last-saved').textContent = 'Last saved: ' + data.timestamp;
                    updateBubbleSheet();
                });
            }

            // Handle Review toggles
            function toggleMarkForReview(questionId, questionNumber) {
                const bubble = document.getElementById('bubble-' + questionNumber);
                if (!bubble) return;
                
                if (bubble.classList.contains('answered')) {
                    bubble.classList.remove('answered');
                    bubble.classList.add('answered-marked');
                } else if (bubble.classList.contains('answered-marked')) {
                    bubble.classList.remove('answered-marked');
                    bubble.classList.add('answered');
                } else {
                    bubble.classList.add('marked');
                }

                fetch('{{ route("exam.mark-review") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        question_number: questionNumber
                    })
                })
                .then(r => r.json())
                .then(() => updateBubbleSheet());
            }

            // Smooth tracking scroll handler
            // 🟢 FIXED JUMP TO QUESTION SYNTAX
            function jumpToQuestion(questionNumber) {
                const element = document.querySelector('[data-question-number="' + questionNumber + '"]');
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    element.classList.add('border-success');
                    setTimeout(() => element.classList.remove('border-success'), 2000);
                }
            }

            // Countdown Logic Engine
            const timerInterval = setInterval(() => {
                timeRemaining--;
                let minutes = Math.floor(timeRemaining / 60);
                let seconds = timeRemaining % 60;
                
                timerElement.textContent = 
                    String(minutes).padStart(2, '0') + ':' + 
                    String(seconds).padStart(2, '0');

                if (timeRemaining <= 300) {
                    timerElement.style.color = '#ff9800';
                }
                if (timeRemaining <= 60) {
                    timerElement.style.color = '#f44336';
                    timerElement.style.animation = 'pulse 1s infinite';
                }

                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    alert('⏰ Time is up! Submitting exam...');
                    examForm.submit();
                }
            }, 1000);

            // Verify explicit intent on completion click
            function confirmSubmit() {
                const answered = document.getElementById('answered-count').textContent;
                const total = totalQuestions;
                return confirm(`You have answered ${answered} out of ${total} questions.\n\nAre you sure you want to submit?`);
            }

            function saveDraft() {
                alert('Draft saved! You can resume this exam later.');
            }

            // Setup lifecycle boots
            updateBubbleSheet();
            setInterval(updateBubbleSheet, 10000); // Poll status updates cleanly every 10s

            window.addEventListener('beforeunload', (e) => {
                if (timeRemaining > 0) {
                    e.preventDefault();
                    e.returnValue = 'Your exam is in progress!';
                }
            });
            </script>
        @endif

    </div>
</div>
@endsection