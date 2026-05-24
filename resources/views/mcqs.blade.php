<!DOCTYPE html>
<html>

<head>
    <title>MCQ Test System</title>
    <style>
        body{
            font-family:Arial, sans-serif;
            background:#f4f6f9;
            padding:20px;
            margin:0;
        }

        .header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            background:white;
            padding:20px;
            border-radius:12px;
            margin-bottom:25px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }

        .title{
            font-size:28px;
            font-weight:bold;
            color:#343a40;
        }

        .timer-container{
            display:flex;
            flex-direction:column;
            align-items:center;
        }

        .timer-tag{
            background:#6f42c1;
            color:white;
            padding:6px 15px;
            border-radius:20px;
            font-size:13px;
            margin-bottom:8px;
            font-weight:bold;
            letter-spacing:1px;
        }

        .timer-box{
            background:#212529;
            color:white;
            padding:12px 22px;
            border-radius:10px;
            font-size:24px;
            font-weight:bold;
            box-shadow:0 0 15px rgba(0,0,0,0.2);
        }

        .start-box{
            background:white;
            padding:30px;
            border-radius:12px;
            margin-bottom:25px;
            text-align:center;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }

        .button-group{
            display:flex;
            gap:15px;
            justify-content:center;
            margin-top:20px;
        }

        .start-btn, .refresh-btn{
            color:white;
            border:none;
            padding:15px 35px;
            font-size:18px;
            border-radius:10px;
            cursor:pointer;
            font-weight:bold;
            transition:0.3s;
        }

        .start-btn{
            background:#198754;
        }

        .start-btn:hover{
            background:#146c43;
        }

        .refresh-btn{
            background:#6c757d;
        }

        .refresh-btn:hover{
            background:#5a6268;
        }

        .mcq-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:25px;
            margin-right:220px;
        }

        @media(max-width:1200px){
            .mcq-grid{
                grid-template-columns:1fr;
                margin-right:0;
            }
            .palette{
                position:relative;
                width:100%;
                top:auto;
                right:auto;
                margin-bottom:25px;
            }
        }

        @media(max-width:768px){
            .header{
                flex-direction:column;
                gap:15px;
                text-align:center;
            }
        }

        .question-card{
            background:white;
            padding:25px;
            border-radius:12px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }

        .question{
            font-size:20px;
            font-weight:bold;
            margin-bottom:20px;
            color:#212529;
            line-height:1.5;
        }

        .option-label{
            display:block;
            padding:15px;
            margin-bottom:12px;
            border:2px solid #dee2e6;
            border-radius:10px;
            cursor:pointer;
            transition:0.3s;
            background:white;
            font-size:16px;
        }

        .option-label:hover{
            background:#f1f3f5;
            border-color:#0d6efd;
        }

        input[type="radio"]{
            margin-right:10px;
            transform:scale(1.2);
        }

        .button-row{
            display:flex;
            gap:10px;
            margin-top:20px;
        }

        .review-btn{
            background:#fd7e14;
            color:white;
            border:none;
            padding:10px 18px;
            border-radius:8px;
            cursor:pointer;
            font-weight:bold;
        }

        .review-btn:hover{
            background:#e96b02;
        }

        .submit-area{
            text-align:center;
            margin-top:40px;
        }

        .submit-btn{
            background:#007bff;
            color:white;
            border:none;
            padding:16px 35px;
            border-radius:10px;
            font-size:18px;
            cursor:pointer;
            transition:0.3s;
        }

        .submit-btn:hover{
            background:#0056b3;
        }

        .palette{
            position:fixed;
            right:20px;
            top:120px;
            width:180px;
            background:white;
            padding:20px;
            border-radius:12px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
            z-index:999;
        }

        .palette-title{
            font-weight:bold;
            margin-bottom:15px;
            text-align:center;
        }

        .palette-grid{
            display:grid;
            grid-template-columns:repeat(5,1fr);
            gap:10px;
        }

        .palette-btn{
            width:30px;
            height:30px;
            border-radius:50%;
            border:none;
            cursor:pointer;
            font-weight:bold;
            background:#dee2e6;
            transition:0.2s;
        }

        .palette-btn:hover{
            transform:scale(1.1);
        }

        .palette-btn.answered{
            background:#198754;
            color:white;
        }

        .palette-btn.review{
            background:#fd7e14 !important;
            color:white;
        }

        .palette-btn.active{
            background:#0d6efd;
            color:white;
        }

        .locked-info{
            background:#e7f3ff;
            border-left:4px solid #0d6efd;
            padding:15px;
            margin-bottom:20px;
            border-radius:5px;
            color:#004085;
        }

    </style>
</head>

<body>

    <form id="testForm" action="{{ url('/submit-test') }}" method="POST">

        @csrf

        <!-- HEADER -->

        <div class="header">

            <div class="title">
                🖊 MCQ Interactive Test System
            </div>

            <div class="timer-container">

                <div class="timer-tag">
                    SHAHJEE BABA
                </div>

                <div class="timer-box">
                    ⏳
                    <span id="timer">
                        {{ $timeRemaining }}:00
                    </span>
                </div>

            </div>

        </div>

        <!-- START / PREVIEW MODE -->

        @if(!$started)

        <div class="start-box">

            <h2>📋 Preview Mode</h2>

            <p>MCQs will reshuffle each time you refresh the page.</p>

            <p style="font-weight:bold; color:#d32f2f;">
                ⚠️ Once you click "Start Test", the session will be LOCKED.
            </p>

            <p style="font-size:14px; color:#666;">
                Time and questions will NOT change even if you refresh the page.
            </p>

            <div class="button-group">

                <form action="{{ url('/refresh-mcqs') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="refresh-btn">
                        🔄 Refresh Questions
                    </button>
                </form>

                <form action="{{ url('/start-test') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="start-btn">
                        ▶️ Start Test
                    </button>
                </form>

            </div>

        </div>

        @else

        <div class="locked-info">
            ✅ <strong>Test Locked:</strong> Session is now locked for anti-cheating. Refresh will NOT change questions or time.
        </div>

        @endif

        <!-- QUESTION PALETTE -->

        <div class="palette">

            <div class="palette-title">
                Questions
            </div>

            <div class="palette-grid">

                @foreach($mcqs as $mcq)

                    <button
                        type="button"
                        class="palette-btn"
                        id="palette{{ $mcq->id }}"
                        onclick="document.getElementById('q{{ $mcq->id }}').scrollIntoView({behavior:'smooth'})">

                        {{ $loop->iteration }}

                    </button>

                @endforeach

            </div>

        </div>

        <!-- MCQ GRID -->

        <div class="mcq-grid">

            @foreach($mcqs as $mcq)

                <div class="question-card" id="q{{ $mcq->id }}">

                    <div class="question">

                        Q{{ $loop->iteration }}:
                        {{ $mcq->question }}

                    </div>

                    <label class="option-label">

                        <input
                            type="radio"
                            name="answers[{{ $mcq->id }}]"
                            value="A"
                            @if(!$started) disabled @endif>

                        <strong>A:</strong>
                        {{ $mcq->option_a }}

                    </label>

                    <label class="option-label">

                        <input
                            type="radio"
                            name="answers[{{ $mcq->id }}]"
                            value="B"
                            @if(!$started) disabled @endif>

                        <strong>B:</strong>
                        {{ $mcq->option_b }}

                    </label>

                    <label class="option-label">

                        <input
                            type="radio"
                            name="answers[{{ $mcq->id }}]"
                            value="C"
                            @if(!$started) disabled @endif>

                        <strong>C:</strong>
                        {{ $mcq->option_c }}

                    </label>

                    <label class="option-label">

                        <input
                            type="radio"
                            name="answers[{{ $mcq->id }}]"
                            value="D"
                            @if(!$started) disabled @endif>

                        <strong>D:</strong>
                        {{ $mcq->option_d }}

                    </label>

                    <div class="button-row">

                        <button
                            type="button"
                            class="review-btn"
                            onclick="markReview({{ $mcq->id }})">

                            Mark Review

                        </button>

                    </div>

                </div>

            @endforeach

        </div>

        <!-- SUBMIT -->

        <div class="submit-area">

            <button
                type="submit"
                class="submit-btn"
                @if(!$started) disabled @endif>

                Submit Test

            </button>

        </div>

    </form>

    <!-- TIMER -->

    <script>

    let timeLeft = {{ $timeRemaining }} * 60;

    const timer = document.getElementById('timer');

    const countdown = setInterval(function(){

        let minutes = Math.floor(timeLeft / 60);

        let seconds = timeLeft % 60;

        seconds = seconds < 10 ? '0' + seconds : seconds;

        timer.innerHTML = minutes + ":" + seconds;

        timeLeft--;

        if(timeLeft < 0){

            clearInterval(countdown);

            alert("Time is over! Test will be submitted.");

            document.getElementById('testForm').submit();
        }

    },1000);

    </script>

    <!-- ANSWERED COLOR -->

    <script>

    document.querySelectorAll('input[type="radio"]').forEach((radio)=>{

        radio.addEventListener('change',function(){

            let questionCard = this.closest('.question-card');

            let questionId = questionCard.id.replace('q','');

            let paletteBtn = document.getElementById('palette' + questionId);

            if(paletteBtn){

                paletteBtn.classList.add('answered');
                paletteBtn.classList.remove('review');

            }

        });

    });

    </script>

    <!-- ACTIVE QUESTION -->

    <script>

    window.addEventListener('scroll', function(){

        document.querySelectorAll('.question-card').forEach((card)=>{

            let rect = card.getBoundingClientRect();

            let questionId = card.id.replace('q','');

            let btn = document.getElementById('palette' + questionId);

            btn.classList.remove('active');

            if(rect.top <= 250 && rect.bottom >= 250){

                btn.classList.add('active');

            }

        });

    });

    </script>

    <!-- REVIEW -->

    <script>

    function markReview(questionId){

        let btn = document.getElementById('palette' + questionId);

        if(btn){

            btn.classList.toggle('review');

        }

    }

    </script>

</body>
</html>