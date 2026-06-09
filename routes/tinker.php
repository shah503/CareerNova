// Check Users
echo "=== USERS ===\n";
User::all()->each(fn($u) => echo $u->id . ": {$u->name} ({$u->role})\n");
echo "Total: " . User::count() . "\n\n";

// Check Subjects
echo "=== SUBJECTS ===\n";
Subject::all()->each(fn($s) => echo $s->id . ": {$s->name}\n");
echo "Total: " . Subject::count() . "\n\n";

// Check MCQs
echo "=== MCQs ===\n";
echo "Total MCQs: " . Mcq::count() . "\n";
echo "Active: " . Mcq::where('status', 'active')->count() . "\n";
echo "Pending Review: " . Mcq::where('status', 'pending_review')->count() . "\n\n";

// Check Exam Sessions
echo "=== EXAM SESSIONS ===\n";
echo "Total Sessions: " . ExamSession::count() . "\n";
echo "Completed: " . ExamSession::where('status', 'completed')->count() . "\n\n";

// Check Answer Logs
echo "=== ANSWER LOGS ===\n";
echo "Total Answer Logs: " . AnswerLog::count() . "\n\n";

// Sample MCQ
echo "=== SAMPLE MCQ ===\n";
$mcq = Mcq::first();
if ($mcq) {
    echo "Q: {$mcq->question}\n";
    echo "Options: {$mcq->option_a}, {$mcq->option_b}, {$mcq->option_c}, {$mcq->option_d}\n";
    echo "Correct: {$mcq->correct_answer}\n";
}

exit