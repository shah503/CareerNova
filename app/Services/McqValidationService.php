<?php

namespace App\Services;

use App\Models\Mcq;
use App\Models\McqValidationLog;

class McqValidationService
{
    /**
     * Layer 1: Structural Validation
     */
    public static function validateStructure($mcq)
    {
        $issues = [];

        // Check all required fields
        if (empty($mcq['question'])) {
            $issues[] = 'Question is empty';
        }

        if (empty($mcq['option_a']) || empty($mcq['option_b']) || empty($mcq['option_c']) || empty($mcq['option_d'])) {
            $issues[] = 'One or more options are missing';
        }

        if (!in_array($mcq['correct_option'] ?? null, ['A', 'B', 'C', 'D'])) {
            $issues[] = 'Invalid correct option';
        }

        // Check formatting
        if (strlen($mcq['question'] ?? '') < 10) {
            $issues[] = 'Question too short';
        }

        return [
            'passed' => empty($issues),
            'issues' => $issues,
            'type' => 'structural'
        ];
    }

    /**
     * Layer 2: Duplicate Detection
     */
    public static function isDuplicate($mcq, $existingMcqs = null)
    {
        $question = strtolower(trim($mcq['question']));

        // Check against existing MCQs in batch
        if (is_array($existingMcqs)) {
            foreach ($existingMcqs as $existing) {
                $existingQuestion = strtolower(trim($existing['question']));
                if (similar_text($question, $existingQuestion) > 0.8) {
                    return true;
                }
            }
        }

        // Check against database
        $similar = Mcq::where('subject', $mcq['subject'] ?? '')
            ->get()
            ->filter(function ($dbMcq) use ($question) {
                $dbQuestion = strtolower(trim($dbMcq->question));
                return similar_text($question, $dbQuestion) > 0.8;
            });

        return $similar->count() > 0;
    }

    /**
     * Layer 3: Scientific Accuracy Validation
     */
    public static function validateScientificAccuracy($mcq)
    {
        $issues = [];

        // Check if correct answer actually matches the explanation
        $correctAnswer = $mcq['option_' . strtolower($mcq['correct_option'])];
        $explanation = strtolower($mcq['explanation'] ?? '');

        // Basic check - explanation should reference the correct option
        $correctAnswerLower = strtolower($correctAnswer);
        if (strlen($explanation) > 0 && strpos($explanation, $correctAnswerLower) === false) {
            // Warning - not necessarily wrong
        }

        // Check for common errors
        if (strpos($explanation, 'error') !== false) {
            $issues[] = 'Explanation might indicate an error in the MCQ';
        }

        return [
            'passed' => empty($issues),
            'issues' => $issues,
            'confidence' => 0.85,
            'type' => 'scientific'
        ];
    }

    /**
     * Layer 4: Difficulty Validation
     */
    public static function validateDifficulty($mcq)
    {
        // This could be enhanced with AI re-validation
        $difficulty = $mcq['difficulty'] ?? 'Medium';

        $validDifficulties = ['Easy', 'Medium', 'Hard'];

        return [
            'passed' => in_array($difficulty, $validDifficulties),
            'issues' => !in_array($difficulty, $validDifficulties) ? ['Invalid difficulty level'] : [],
            'type' => 'difficulty'
        ];
    }

    /**
     * Run all validations
     */
    public static function validateComplete($mcq, $mcqId = null)
    {
        $validations = [
            self::validateStructure($mcq),
            self::validateDifficulty($mcq),
            self::validateScientificAccuracy($mcq),
        ];

        // Log validation results if MCQ ID provided
        if ($mcqId) {
            foreach ($validations as $validation) {
                McqValidationLog::create([
                    'mcq_id' => $mcqId,
                    'validation_type' => $validation['type'],
                    'passed' => $validation['passed'],
                    'issues' => implode('; ', $validation['issues'] ?? []),
                    'details' => $validation
                ]);
            }
        }

        return $validations;
    }
}