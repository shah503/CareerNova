<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamSession;
use App\Models\User;
use App\Models\Subject;

class ExamSessionSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $subjects = Subject::all();

        $examCount = 0;

        foreach ($students as $student) {
            // Each student takes 3-5 exams
            $examCount = rand(3, 5);

            for ($i = 0; $i < $examCount; $i++) {
                $subject = $subjects->random();
                $correctAnswers = rand(5, 15);
                $totalQuestions = 20;
                $percentage = ($correctAnswers / $totalQuestions) * 100;

                ExamSession::create([
                    'user_id' => $student->id,
                    'subject_id' => $subject->id,
                    'total_questions' => $totalQuestions,
                    'score' => $correctAnswers,
                    'correct_answers' => $correctAnswers,
                    'wrong_answers' => $totalQuestions - $correctAnswers,
                    'percentage' => $percentage,
                    'started_at' => now()->subDays(rand(1, 30)),
                    'finished_at' => now()->subDays(rand(1, 30)),
                    'status' => 'completed',
                ]);
            }
        }

        echo "✅ Created exam sessions for students\n";
    }
}