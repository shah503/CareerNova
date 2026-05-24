<?php

namespace App\Services;

use App\Models\Mcq;
use App\Models\ExamSession;
use Illuminate\Database\Eloquent\Collection;

class McqRandomizationService
{
    /**
     * Get randomized MCQs for an exam session
     * 
     * @param int $subjectId
     * @param int $count
     * @return Collection
     */
    public function getRandomMcqs($subjectId, $count = 10)
    {
        return Mcq::where('subject_id', $subjectId)
            ->where('status', 'active')
            ->inRandomOrder()
            ->limit($count)
            ->get();
    }

    /**
     * Get randomized MCQs by difficulty level
     * 
     * @param int $subjectId
     * @param int $count
     * @param array $difficultyDistribution ['easy' => 30, 'medium' => 50, 'hard' => 20]
     * @return Collection
     */
    public function getRandomMcqsByDifficulty($subjectId, $count = 10, $difficultyDistribution = [])
    {
        if (empty($difficultyDistribution)) {
            $difficultyDistribution = [
                'easy' => 0.3,
                'medium' => 0.5,
                'hard' => 0.2,
            ];
        }

        $mcqs = collect();

        foreach ($difficultyDistribution as $difficulty => $percentage) {
            $questionCount = ceil($count * $percentage);

            $questions = Mcq::where('subject_id', $subjectId)
                ->where('difficulty', $difficulty)
                ->where('status', 'active')
                ->inRandomOrder()
                ->limit($questionCount)
                ->get();

            $mcqs = $mcqs->merge($questions);
        }

        return $mcqs->shuffle()->take($count);
    }

    /**
     * Randomize option order for a question
     * 
     * @param Mcq $mcq
     * @return array
     */
    public function randomizeOptions($mcq)
    {
        $options = [
            'A' => $mcq->option_a,
            'B' => $mcq->option_b,
            'C' => $mcq->option_c,
            'D' => $mcq->option_d,
        ];

        $shuffled = collect($options)->shuffle();

        $mapping = [];
        $newCorrectAnswer = null;
        $newOptions = [];

        foreach ($shuffled as $newLetter => $optionText) {
            $newOptions[$newLetter] = $optionText;

            // Map original correct answer to new position
            if ($optionText === $mcq->getOptionByLetter($mcq->correct_answer)) {
                $newCorrectAnswer = $newLetter;
            }
        }

        return [
            'options' => $newOptions,
            'correct_answer' => $newCorrectAnswer,
            'mapping' => $mapping, // For tracking original positions if needed
        ];
    }

    /**
     * Store randomized MCQs order in answer logs
     * 
     * @param ExamSession $session
     * @param Collection $mcqs
     * @return void
     */
    public function recordMcqOrder(ExamSession $session, Collection $mcqs)
    {
        $mcqs->each(function ($mcq, $index) use ($session) {
            // The order will be stored when answer is logged
            // This is a helper to track the sequence
        });
    }

    /**
     * Reshuffle MCQs for session refresh
     * (Only if session is not locked)
     * 
     * @param ExamSession $session
     * @return Collection|null
     */
    public function reshuffleMcqs(ExamSession $session)
    {
        if ($session->is_locked) {
            return null;
        }

        // Get current MCQ IDs from answer logs or session
        $currentMcqIds = $session->answerLogs()->pluck('mcq_id')->toArray();

        // Return same MCQs in different order
        return Mcq::whereIn('id', $currentMcqIds)
            ->inRandomOrder()
            ->get();
    }
}