<!DOCTYPE html>
<html>
<head>
    <title>Test Result</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 30px;
            margin: 0;
        }

        .header-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .score-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .results-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .question-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .question-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .option-row {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 12px;
            background: #f1f3f5;
            color: #333;
            border: 2px solid #d1d1d1;
            flex-shrink: 0;
        }

        /* ✅ FIXED: Correct answer when selected */
        .circle.correct-choice {
            background: #28a745 !important;
            color: white !important;
            border: none !important;
        }

        /* ❌ FIXED: Wrong answer when selected */
        .circle.wrong-choice {
            background: #dc3545 !important;
            color: white !important;
            border: none !important;
        }

        /* ✅ FIXED: Show correct answer only in REVIEW MODE */
        .circle.correct-indicator {
            border: 2px solid #28a745;
            background: #e8f5e9;
            color: #1b5e20;
        }

        /* Default state - for unanswered */
        .circle.default-state {
            background: #f1f3f5;
            color: #333;
            border: 2px solid #d1d1d1;
        }

        .option-text {
            font-size: 16px;
            line-height: 1.5;
        }

        .answer-box {
            margin-top: 20px;
        }

        .correct-answer {
            padding: 12px;
            border-radius: 8px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            font-size: 15px;
            line-height: 1.6;
        }

        /* ANALYSIS BOX */
        .analysis-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .analysis-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color:#212529;
            text-align: center;
        }   

        .analysis-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .analysis-card {
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            color: white;
            font-weight: bold;
        }

        .total-card { background: #0d6efd; }
        .attempted-card { background: #0d6efd; }
        .correct-card { background: #198754; }
        .wrong-card { background: #dc3545; }
        .unanswered-card { background: #6c757d; }

        .analysis-number {
            font-size: 32px;
            margin-top: 10px;
        }

        /* Charts Section */
        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .chart-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Mode Toggle */
        .mode-toggle {
            display: flex;
            gap: 10px;
        }

        .mode-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .mode-btn.active {
            background: #007bff;
            color: white;
        }

        .mode-btn.inactive {
            background: #e9ecef;
            color: #495057;
        }

        .mode-section {
            display: none;
        }

        .mode-section.active {
            display: block;
        }

        .btn {
            display: inline-block;
            margin-top: 30px;
            padding: 14px 25px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .center {
            text-align: center;
        }

        @media(max-width:768px){
            .analysis-grid { grid-template-columns: 1fr 1fr; }
            .charts-container { grid-template-columns: 1fr; }
        }

        @media(max-width:900px){
            .results-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    {{-- HEADER WITH MODE TOGGLE --}}
    <div class="header-section">
        <h1>🖊 Test Result</h1>
        <div class="mode-toggle">
            <button class="mode-btn active" onclick="switchMode('result')">📊 Result View</button>
            <button class="mode-btn inactive" onclick="switchMode('review')">🔍 Review Mode</button>
        </div>
    </div>

    {{-- SCORE BOX --}}
    <div class="score-box">
        <h2>Your Score: {{ $examSession->score ?? 0 }} / {{ $examSession->total_questions ?? 0 }}</h2>
        <h3>Grade: <span style="color: {{ $grade['color'] }};">{{ $grade['grade'] }} - {{ $grade['remarks'] }}</span></h3>
        <p>Percentage: {{ $examSession->percentage ?? 0 }}%</p>
    </div>

    {{-- RESULT VIEW MODE --}}
    <div id="result-mode" class="mode-section active">
        {{-- ANALYSIS BOX --}}
        <div class="analysis-box">
            <div class="analysis-title">📊 Performance Analysis</div>
            <div class="analysis-grid">
                <div class="analysis-card total-card">
                    Total Questions
                    <div class="analysis-number">
                        {{ $examSession->total_questions ?? 0 }}
                    </div>
                </div>

                <div class="analysis-card attempted-card">
                    Attempted
                    <div class="analysis-number">
                        {{ ($examSession->correct_answers ?? 0) + ($examSession->wrong_answers ?? 0) }}
                    </div>
                    <small style="display:block; margin-top:5px;">✓ Correct + ✗ Wrong</small>
                </div>

                <div class="analysis-card correct-card">
                    Correct Answers
                    <div class="analysis-number">{{ $examSession->correct_answers ?? 0 }}</div>
                    <small style="display:block; margin-top:5px;">✓ Correct</small>
                </div>

                <div class="analysis-card wrong-card">
                    Incorrect Answers
                    <div class="analysis-number">{{ $examSession->wrong_answers ?? 0 }}</div>
                    <small style="display:block; margin-top:5px;">✗ Wrong</small>
                </div>

                <div class="analysis-card unanswered-card">
                    Unanswered
                    <div class="analysis-number">{{ $examSession->unanswered_count ?? 0 }}</div>
                    <small style="display:block; margin-top:5px;">? Skipped</small>
                </div>
            </div>
        </div>

        {{-- CHARTS --}}
        <div class="charts-container">
            {{-- Score Distribution Pie Chart --}}
            <div class="chart-box">
                <div class="chart-title">Score Distribution</div>
                <canvas id="scoreChart"></canvas>
            </div>

            {{-- Difficulty Breakdown Chart --}}
            <div class="chart-box">
                <div class="chart-title">Performance by Difficulty</div>
                <canvas id="difficultyChart"></canvas>
            </div>
        </div>
    </div>

    {{-- REVIEW MODE --}}
    <div id="review-mode" class="mode-section">
        <div class="analysis-box">
            <div class="analysis-title">🔍 Review Mode - Detailed Analysis</div>
            <p style="text-align: center; color: #666;">
                Below you can see the correct answers highlighted for learning purposes.
            </p>
        </div>
    </div>

    {{-- QUESTION REVIEW SECTION (VISIBLE IN BOTH MODES) --}}
    <h2 style="margin-top: 40px; margin-bottom: 20px;">📋 Complete Answer Review Sheet</h2>
    
    <div class="results-grid">
    @forelse($results as $result)
        @php
            $studentAns = $result['student_answer'] ?? null;
            $correctAns = $result['correct_answer'] ?? null;
            $isCorrect = $result['is_correct'] ?? false;
        @endphp

        <div class="question-card">
            <div class="question-title">
                {{ $result['question'] }}
                @if($isCorrect)
                    <span style="color: #28a745; font-size: 14px;"> ✓ Correct</span>
                @elseif($studentAns)
                    <span style="color: #dc3545; font-size: 14px;"> ✗ Incorrect</span>
                @else
                    <span style="color: #6c757d; font-size: 14px;"> ? Unanswered</span>
                @endif
            </div>

            {{-- OPTION A --}}
            <div class="option-row">
                <div class="circle 
                    @if($studentAns === 'A' && $correctAns === 'A')
                        correct-choice
                    @elseif($studentAns === 'A' && $correctAns !== 'A')
                        wrong-choice
                    @elseif($studentAns !== 'A' && $correctAns === 'A' && session('review_mode'))
                        correct-indicator
                    @else
                        default-state
                    @endif
                ">
                    A
                </div>
                <div class="option-text">{{ $result['option_a'] }}</div>
            </div>

            {{-- OPTION B --}}
            <div class="option-row">
                <div class="circle 
                    @if($studentAns === 'B' && $correctAns === 'B')
                        correct-choice
                    @elseif($studentAns === 'B' && $correctAns !== 'B')
                        wrong-choice
                    @elseif($studentAns !== 'B' && $correctAns === 'B' && session('review_mode'))
                        correct-indicator
                    @else
                        default-state
                    @endif
                ">
                    B
                </div>
                <div class="option-text">{{ $result['option_b'] }}</div>
            </div>

            {{-- OPTION C --}}
            <div class="option-row">
                <div class="circle 
                    @if($studentAns === 'C' && $correctAns === 'C')
                        correct-choice
                    @elseif($studentAns === 'C' && $correctAns !== 'C')
                        wrong-choice
                    @elseif($studentAns !== 'C' && $correctAns === 'C' && session('review_mode'))
                        correct-indicator
                    @else
                        default-state
                    @endif
                ">
                    C
                </div>
                <div class="option-text">{{ $result['option_c'] }}</div>
            </div>

            {{-- OPTION D --}}
            <div class="option-row">
                <div class="circle 
                    @if($studentAns === 'D' && $correctAns === 'D')
                        correct-choice
                    @elseif($studentAns === 'D' && $correctAns !== 'D')
                        wrong-choice
                    @elseif($studentAns !== 'D' && $correctAns === 'D' && session('review_mode'))
                        correct-indicator
                    @else
                        default-state
                    @endif
                ">
                    D
                </div>
                <div class="option-text">{{ $result['option_d'] }}</div>
            </div>

            @if(session('review_mode') || true)
                <div class="answer-box">
                    <div class="correct-answer">
                        <strong>Correct Answer (Option {{ $correctAns }}):</strong>
                        <p style="margin: 5px 0 0 0; color: #555;">{{ $result['explanation'] ?: 'No explanation available.' }}</p>
                    </div>
                </div>
            @endif
        </div>
    @empty
        <div style="grid-column: span 2; padding: 20px; background: white; text-align: center; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            No evaluation data logs found for this session.
        </div>
    @endforelse
    </div>

    <div class="center">
        <button class="btn" onclick="window.location.href='{{ route('exam.select-subject') }}'">🔄 Take Another Exam</button>
    </div>

    <script>
        // Score Distribution Chart
        const scoreCtx = document.getElementById('scoreChart').getContext('2d');
        const scoreChart = new Chart(scoreCtx, {
            type: 'pie',
            data: {
                labels: ['Correct', 'Incorrect', 'Unanswered'],
                datasets: [{
                    data: [
                        {{ $examSession->correct_answers ?? 0 }},
                        {{ $examSession->wrong_answers ?? 0 }},
                        {{ $examSession->unanswered_count ?? 0 }}
                    ],
                    backgroundColor: ['#28a745', '#dc3545', '#6c757d'],
                    borderColor: ['#1e7e34', '#bb2d3b', '#545b62'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Difficulty Chart
        const difficultyCtx = document.getElementById('difficultyChart').getContext('2d');
        const difficultyChart = new Chart(difficultyCtx, {
            type: 'bar',
            data: {
                labels: ['Easy', 'Medium', 'Hard'],
                datasets: [
                    {
                        label: 'Correct',
                        data: [
                            {{ $difficultyBreakdown['easy']['correct'] ?? 0 }},
                            {{ $difficultyBreakdown['medium']['correct'] ?? 0 }},
                            {{ $difficultyBreakdown['hard']['correct'] ?? 0 }}
                        ],
                        backgroundColor: '#28a745'
                    },
                    {
                        label: 'Incorrect',
                        data: [
                            {{ ($difficultyBreakdown['easy']['total'] ?? 0) - ($difficultyBreakdown['easy']['correct'] ?? 0) }},
                            {{ ($difficultyBreakdown['medium']['total'] ?? 0) - ($difficultyBreakdown['medium']['correct'] ?? 0) }},
                            {{ ($difficultyBreakdown['hard']['total'] ?? 0) - ($difficultyBreakdown['hard']['correct'] ?? 0) }}
                        ],
                        backgroundColor: '#dc3545'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Mode switching function
        function switchMode(mode) {
            // Update session
            fetch('{{ route("exam.set-review-mode") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ review_mode: mode === 'review' })
            });

            // Update UI
            document.getElementById('result-mode').classList.toggle('active', mode === 'result');
            document.getElementById('review-mode').classList.toggle('active', mode === 'review');
            
            document.querySelectorAll('.mode-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.classList.add('inactive');
            });
            
            event.target.classList.remove('inactive');
            event.target.classList.add('active');

            // Reload to show/hide correct answers
            location.reload();
        }
    </script>

</body>
</html>