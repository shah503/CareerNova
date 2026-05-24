<?php

namespace App\Http\Controllers;

use App\Models\Mcq;
use App\Models\ExamSession;
use App\Models\AnswerLog;
use App\Models\StudentPoints;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class McqController extends Controller
{
    /**
     * Show test page with locked session
     */
    public function index()
    {
        // Check if chatbot is enabled
        $chatbotEnabled = SystemSetting::isModuleEnabled('chatbot');

        if (session('exam_submitted')) {
            return redirect('/student-dashboard');
        }

        if (session()->has('locked_mcqs')) {
            $mcqs = collect(session('locked_mcqs'));
            $started = true;
            $timeRemaining = session('time_remaining', 0);
        } else {
            $mcqs = $this->generateMcqs();
            $started = false;
            $timeRemaining = $mcqs->count() * 60;
        }

        return view('mcqs', compact('mcqs', 'started', 'timeRemaining', 'chatbotEnabled'));
    }

    /**
     * Start test - Lock session
     */
    public function startTest(Request $request)
    {
        if (session()->has('locked_mcqs')) {
            return redirect('/mcqs');
        }

        $mcqs = session('current_mcqs', $this->generateMcqs());
        $totalMcqs = $mcqs->count();
        $timeInSeconds = $totalMcqs * 60;

        session([
            'locked_mcqs' => $mcqs,
            'started' => true,
            'start_time' => now(),
            'time_remaining' => $timeInSeconds
        ]);

        session()->forget('current_mcqs');

        return redirect('/mcqs');
    }

    /**
     * Auto-save answer
     */
    public function saveAnswer(Request $request)
    {
        if (!session()->has('locked_mcqs')) {
            return response()->json(['error' => 'Test not started'], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|integer',
            'answer' => 'nullable|in:A,B,C,D',
            'is_review' => 'boolean'
        ]);

        try {
            AnswerLog::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'mcq_id' => $validated['question_id']
                ],
                [
                    'answer' => $validated['answer'],
                    'is_review' => $validated['is_review'] ?? false,
                    'answered_at' => now()
                ]
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Save failed'], 500);
        }
    }

    /**
     * Submit test - Complete with validation
     */
    public function submitTest(Request $request)
    {
        if (!session()->has('locked_mcqs')) {
            return redirect('/mcqs');
        }

        if (session('exam_submitted')) {
            return redirect('/student-dashboard');
        }

        $score = 0;
        $correct_answers = 0;
        $results = [];
        $answers = $request->answers ?? [];
        $lockedMcqs = session('locked_mcqs');
        $subjectStats = [];

        $startTime = session('start_time');
        $timeTaken = now()->diffInSeconds($startTime);

        foreach ($answers as $questionId => $studentAnswer) {
            $mcq = $lockedMcqs->firstWhere('id', $questionId);

            if (!$mcq) continue;

            $isCorrect = $studentAnswer == $mcq->correct_option;

            if ($isCorrect) {
                $score++;
                $correct_answers++;
            }

            if (!isset($subjectStats[$mcq->subject])) {
                $subjectStats[$mcq->subject] = ['correct' => 0, 'total' => 0];
            }
            $subjectStats[$mcq->subject]['total']++;
            if ($isCorrect) {
                $subjectStats[$mcq->subject]['correct']++;
            }

            $results[] = [
                'question_id' => $mcq->id,
                'question' => $mcq->question,
                'option_a' => $mcq->option_a,
                'option_b' => $mcq->option_b,
                'option_c' => $mcq->option_c,
                'option_d' => $mcq->option_d,
                'student_answer' => $studentAnswer,
                'correct_option' => $mcq->correct_option,
                'explanation' => $mcq->explanation,
                'subject' => $mcq->subject,
                'is_correct' => $isCorrect
            ];
        }

        // Create exam session
        $examSession = auth()->user()->examSessions()->create([
            'total_questions' => count($results),
            'score' => $score,
            'correct_answers' => $correct_answers,
            'time_taken' => $timeTaken,
            'answers' => json_encode($answers),
            'subject_breakdown' => json_encode($subjectStats),
            'status' => 'submitted',
            'completed_at' => now()
        ]);

        // Award points
        StudentPoints::awardPoints(auth()->id(), $correct_answers, count($results));

        // Update user stats
        auth()->user()->update([
            'total_tests' => auth()->user()->total_tests + 1,
            'average_score' => (auth()->user()->average_score * (auth()->user()->total_tests - 1) + ($correct_answers / count($results) * 100)) / auth()->user()->total_tests,
        ]);

        // Clear session
        session()->forget(['locked_mcqs', 'started', 'start_time', 'time_remaining', 'current_mcqs']);

        // Lock for security
        session(['exam_submitted' => true, 'exam_session_id' => $examSession->id]);

        return view('result', compact('score', 'results', 'subjectStats', 'examSession'));
    }

    /**
     * Generate MCQs with proper distribution
     */
    private function generateMcqs()
    {
        $biologyEasy = Mcq::where('subject', 'Biology')
            ->where('difficulty', 'Easy')
            ->verified()
            ->inRandomOrder()
            ->take(6)
            ->get();

        $biologyMedium = Mcq::where('subject', 'Biology')
            ->where('difficulty', 'Medium')
            ->verified()
            ->inRandomOrder()
            ->take(6)
            ->get();

        $biologyHard = Mcq::where('subject', 'Biology')
            ->where('difficulty', 'Hard')
            ->verified()
            ->inRandomOrder()
            ->take(5)
            ->get();

        $biology = $biologyEasy->merge($biologyMedium)->merge($biologyHard);

        $chemistry = Mcq::where('subject', 'Chemistry')
            ->verified()
            ->inRandomOrder()
            ->take(13)
            ->get();

        $physics = Mcq::where('subject', 'Physics')
            ->verified()
            ->inRandomOrder()
            ->take(13)
            ->get();

        $english = Mcq::where('subject', 'English')
            ->verified()
            ->inRandomOrder()
            ->take(5)
            ->get();

        $reasoning = Mcq::where('subject', 'Analytical Reasoning')
            ->verified()
            ->inRandomOrder()
            ->take(2)
            ->get();

        return $biology->merge($chemistry)->merge($physics)->merge($english)->merge($reasoning)->shuffle();
    }

    /**
     * Add query scope to check verified status
     */
    public static function scopeVerified($query)
    {
        return $query->where('verified', true)->where('needs_review', false);
    }
}