<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Result</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 30px;
            margin: 0;
        }

        .student-session-block {
            margin-bottom: 60px;
            border-bottom: 3px dashed #b2bec3;
            padding-bottom: 40px;
        }

        .student-header {
            background: #2d3436;
            color: white;
            padding: 15px 25px;
            border-radius: 12px 12px 0 0;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        .score-box {
            background: white;
            padding: 20px;
            border-radius: 0 0 12px 12px;
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

        /* Pure colors with no extra labels beneath */
        .circle.correct-choice {
            background: #28a745 !important;
            color: white !important;
            border: none !important;
        }

        .circle.wrong-choice {
            background: #dc3545 !important;
            color: white !important;
            border: none !important;
        }

        .circle.highlighted-answer {
            border: 2px solid #28a745;
            background: #e8f5e9;
            color: #1b5e20;
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
        .correct-card { background: #198754; }
        .wrong-card { background: #dc3545; }
        .unanswered-card { background: #6c757d; }

        .analysis-number {
            font-size: 32px;
            margin-top: 10px;
        }

        .pagination-container {
            margin-top: 40px;
            display: flex;
            justify-content: center;
        }

        @media(max-width:768px){
            .analysis-grid { grid-template-columns: 1fr 1fr; }
        }

        @media(max-width:900px){
            .results-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <h1 style="margin-bottom: 30px; color: #212529; text-align: center;">🏫 Teacher Portal: Student Exam Review Sheets</h1>

    @forelse($results as $examSession)
        <div class="student-session-block">
            
            <div class="student-header">
                <span>👤 Student: {{ $examSession->user->name ?? 'Unknown Student' }}</span>
                <span>📚 Subject: {{ $examSession->subject->name ?? 'N/A' }}</span>
            </div>

            <div class="score-box">
                <h1>🖊 Test Result</h1>
                <h2>Your Score: {{ $examSession->score ?? 0 }}%</h2>
            </div>

            <div class="analysis-box">
                <div class="analysis-title">📊 Performance Analysis</div>
                <div class="analysis-grid">
                    <div class="analysis-card total-card">
                        Total Questions
                        <div class="analysis-number">
                            {{ ($examSession->correct_answers ?? 0) + ($examSession->wrong_answers ?? 0) + ($examSession->unanswered_count ?? 0) }}
                        </div>
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

            <h2 style="margin-top: 40px; margin-bottom: 20px;">📋 Complete Answer Review Sheet</h2>
            
            <div class="results-grid">
            {{-- Safely decoding json or using arrays depending on how questions are stored inside your session log --}}
            @php 
                $sessionQuestions = is_string($examSession->questions_log) ? json_decode($examSession->questions_log, true) : ($examSession->questions_log ?? []);
            @endphp

            @forelse($sessionQuestions as $result)
                @php
                    $studentAns = $result['student_answer'] ?? null;
                    $correctAns = $result['correct_answer'] ?? null; 
                @endphp

                <div class="question-card">
                    <div class="question-title">
                        {{ $result['question'] ?? 'Question Text Missing' }}
                    </div>

                    {{-- OPTION A --}}
                    <div class="option-row">
                        <div class="circle 
                            {{ $studentAns === 'A' && $correctAns === 'A' ? 'correct-choice' : '' }}
                            {{ $studentAns === 'A' && $correctAns !== 'A' ? 'wrong-choice' : '' }}
                            {{ $studentAns !== 'A' && $correctAns === 'A' ? 'highlighted-answer' : '' }}
                        ">
                            A
                        </div>
                        <div class="option-text">{{ $result['option_a'] ?? '' }}</div>
                    </div>

                    {{-- OPTION B --}}
                    <div class="option-row">
                        <div class="circle 
                            {{ $studentAns === 'B' && $correctAns === 'B' ? 'correct-choice' : '' }}
                            {{ $studentAns === 'B' && $correctAns !== 'B' ? 'wrong-choice' : '' }}
                            {{ $studentAns !== 'B' && $correctAns === 'B' ? 'highlighted-answer' : '' }}
                        ">
                            B
                        </div>
                        <div class="option-text">{{ $result['option_b'] ?? '' }}</div>
                    </div>

                    {{-- OPTION C --}}
                    <div class="option-row">
                        <div class="circle 
                            {{ $studentAns === 'C' && $correctAns === 'C' ? 'correct-choice' : '' }}
                            {{ $studentAns === 'C' && $correctAns !== 'C' ? 'wrong-choice' : '' }}
                            {{ $studentAns !== 'C' && $correctAns === 'C' ? 'highlighted-answer' : '' }}
                        ">
                            C
                        </div>
                        <div class="option-text">{{ $result['option_c'] ?? '' }}</div>
                    </div>

                    {{-- OPTION D --}}
                    <div class="option-row">
                        <div class="circle 
                            {{ $studentAns === 'D' && $correctAns === 'D' ? 'correct-choice' : '' }}
                            {{ $studentAns === 'D' && $correctAns !== 'D' ? 'wrong-choice' : '' }}
                            {{ $studentAns !== 'D' && $correctAns === 'D' ? 'highlighted-answer' : '' }}
                        ">
                            D
                        </div>
                        <div class="option-text">{{ $result['option_d'] ?? '' }}</div>
                    </div>

                    <div class="answer-box">
                        <div class="correct-answer">
                            <strong>Correct Answer (Option {{ $correctAns }}):</strong>
                            <p style="margin: 5px 0 0 0; color: #555;">{{ $result['explanation'] ?? 'No explanation available.' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: span 2; padding: 20px; background: white; text-align: center; border-radius: 12px;">
                    No evaluation question logs found inside this specific exam file.
                </div>
            @endforelse
            </div>
        </div>
    @empty
        <div style="padding: 40px; background: white; text-align: center; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h2>No Submissions Found</h2>
            <p style="color: #6c757d; margin: 0;">No students have taken tests corresponding to your subjects yet.</p>
        </div>
    @endforelse

    <div class="pagination-container">
        {{ $results->links() }}
    </div>

</body>
</html>