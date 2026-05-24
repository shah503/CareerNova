<!DOCTYPE html>
<html>

<head>

    <title>Test Result</title>

    <style>

        body{
            font-family:Arial;
            background:#f4f6f9;
            padding:30px;
            margin:0;
        }

        .score-box{
            background:white;
            padding:20px;
            border-radius:12px;
            margin-bottom:30px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
            text-align:center;
        }

        .results-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
        }

        .question-card{
            background:white;
            padding:20px;
            border-radius:12px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }

        .question-title{
            font-size:18px;
            font-weight:bold;
            margin-bottom:20px;
            line-height:1.5;
        }

        .option-row{
            display:flex;
            align-items:center;
            margin-bottom:12px;
        }

        .circle{
            width:38px;
            height:38px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:bold;
            margin-right:12px;
            background:#f1f3f5;
            color:#333;
            border:2px solid #d1d1d1;
            flex-shrink:0;
        }

        .correct{
            background:#28a745;
            color:white;
            border:none;
        }

        .wrong{
            background:#dc3545;
            color:white;
            border:none;
        }

        .unanswered{
            background:#f1f3f5;
            color:#333;
            border:2px solid #d1d1d1;
        }

        .option-text{
            font-size:16px;
            line-height:1.5;
        }

        .answer-box{
            margin-top:20px;
        }

        .correct-answer{
            padding:12px;
            border-radius:8px;
            background:#f8f9fa;
            border-left:4px solid #007bff;
            font-size:15px;
            line-height:1.6;
        }

        .btn{
            display:inline-block;
            margin-top:30px;
            padding:14px 25px;
            background:#007bff;
            color:white;
            text-decoration:none;
            border-radius:8px;
            font-size:16px;
        }

        .center{
            text-align:center;
        }

        /* ANALYSIS BOX */

        .analysis-box{
            background:white;
            padding:25px;
            border-radius:12px;
            margin-bottom:25px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }

        .analysis-title{
            font-size:24px;
            font-weight:bold;
            margin-bottom:20px;
            color:#212529;
            text-align:center;
        }   

        .analysis-grid{
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:20px;
        }

        .analysis-card{
            padding:20px;
            border-radius:12px;
            text-align:center;
            color:white;
            font-weight:bold;
        }

        .total-card{
            background:#0d6efd;
        }

        .correct-card{
            background:#198754;
        }

        .wrong-card{
            background:#dc3545;
        }

        .unanswered-card{
            background:#6c757d;
        }

        .analysis-number{
            font-size:32px;
            margin-top:10px;
        }

        @media(max-width:768px){

            .analysis-grid{
                grid-template-columns:1fr 1fr;
            }

        }

        @media(max-width:900px){

            .results-grid{
                grid-template-columns:1fr;
            }

        }

    </style>

</head>

<body>

    <div class="score-box">

        <h1>🖊 Test Result</h1>

        <h2>Your Score: {{ $score }}</h2>

    </div>

    <!-- ANALYSIS BOX -->

    <div class="analysis-box">

        <div class="analysis-title">
            📊 Performance Analysis
        </div>

        <div class="analysis-grid">

            @php

                $totalQuestions = count($results);

                $correctAnswers = $score;

                $wrongAnswers =
                    count(
                        array_filter(
                            $results,
                            fn($r) =>
                            $r['student_answer'] &&
                            $r['student_answer'] != $r['correct_option']
                        )
                    );

                $unanswered =
                    $totalQuestions -
                    ($correctAnswers + $wrongAnswers);

            @endphp

            <div class="analysis-card total-card">

                Total Questions

                <div class="analysis-number">
                    {{ $totalQuestions }}
                </div>

            </div>

            <div class="analysis-card correct-card">

                Correct

                <div class="analysis-number">
                    {{ $correctAnswers }}
                </div>

            </div>

            <div class="analysis-card wrong-card">

                Wrong

                <div class="analysis-number">
                    {{ $wrongAnswers }}
                </div>

            </div>

            <div class="analysis-card unanswered-card">

                Unanswered

                <div class="analysis-number">
                    {{ $unanswered }}
                </div>

            </div>

        </div>

    </div>
        <div class="analysis-box">

        <h2>📊 Subject Analysis</h2>

        @foreach($subjectStats as $subject => $stats)

            <div class="analysis-row">

                <strong>{{ $subject }}</strong>

                <span>
                    {{ $stats['correct'] }}
                    /
                    {{ $stats['total'] }}
                </span>

            </div>

        @endforeach

    </div>

    </div>

    <div class="results-grid">

    @foreach($results as $result)

        <div class="question-card">

            <div class="question-title">

                {{ $result['question'] }}

            </div>

            {{-- OPTION A --}}
            <div class="option-row">

                <div class="circle
                    {{ $result['student_answer'] == 'A' && $result['correct_option'] == 'A' ? 'correct' : '' }}
                    {{ $result['student_answer'] == 'A' && $result['correct_option'] != 'A' ? 'wrong' : '' }}
                    {{ empty($result['student_answer']) && $result['correct_option'] == 'A' ? 'unanswered' : '' }}
                ">
                    A
                </div>

                <div class="option-text">

                    {{ $result['option_a'] }}

                </div>

            </div>

            {{-- OPTION B --}}
            <div class="option-row">

                <div class="circle
                    {{ $result['student_answer'] == 'B' && $result['correct_option'] == 'B' ? 'correct' : '' }}
                    {{ $result['student_answer'] == 'B' && $result['correct_option'] != 'B' ? 'wrong' : '' }}
                    {{ empty($result['student_answer']) && $result['correct_option'] == 'B' ? 'unanswered' : '' }}
                ">
                    B
                </div>

                <div class="option-text">

                    {{ $result['option_b'] }}

                </div>

            </div>

            {{-- OPTION C --}}
            <div class="option-row">

                <div class="circle
                    {{ $result['student_answer'] == 'C' && $result['correct_option'] == 'C' ? 'correct' : '' }}
                    {{ $result['student_answer'] == 'C' && $result['correct_option'] != 'C' ? 'wrong' : '' }}
                    {{ empty($result['student_answer']) && $result['correct_option'] == 'C' ? 'unanswered' : '' }}
                ">
                    C
                </div>

                <div class="option-text">

                    {{ $result['option_c'] }}

                </div>

            </div>

            {{-- OPTION D --}}
            <div class="option-row">

                <div class="circle
                    {{ $result['student_answer'] == 'D' && $result['correct_option'] == 'D' ? 'correct' : '' }}
                    {{ $result['student_answer'] == 'D' && $result['correct_option'] != 'D' ? 'wrong' : '' }}
                    {{ empty($result['student_answer']) && $result['correct_option'] == 'D' ? 'unanswered' : '' }}
                ">
                    D
                </div>

                <div class="option-text">

                    {{ $result['option_d'] }}

                </div>

            </div>

            <div class="answer-box">

                <div class="correct-answer">

                    <strong>
                        {{ $result['correct_option'] }}:
                    </strong>

                    {{ $result['explanation'] }}

                </div>

            </div>

        </div>

    @endforeach

    </div>

    <div class="center">

        <a href="/mcqs" class="btn">

            🔄 Retake Test

        </a>

    </div>

</body>

</html>