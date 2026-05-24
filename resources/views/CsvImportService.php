<?php

namespace App\Services;

use App\Models\Mcq;
use App\Models\Subject;
use Illuminate\Http\UploadedFile;
use Exception;

class CsvImportService
{
    /**
     * Import MCQs from CSV file
     * 
     * @param UploadedFile $file
     * @param int $subjectId
     * @param int $userId
     * @return array
     */
    public function importMcqsFromCsv(UploadedFile $file, $subjectId, $userId)
    {
        $results = [
            'total' => 0,
            'imported' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        try {
            $path = $file->getRealPath();
            $csv = array_map('str_getcsv', file($path));

            // Get headers (first row)
            $headers = array_shift($csv);
            $headerMap = $this->mapHeaders($headers);

            foreach ($csv as $row => $data) {
                $results['total']++;

                try {
                    $mcqData = $this->parseMcqRow($data, $headerMap, $subjectId, $userId);
                    
                    Mcq::create($mcqData);
                    $results['imported']++;
                } catch (Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'row' => $row + 2, // +2 because we removed header row
                        'error' => $e->getMessage(),
                    ];
                }
            }
        } catch (Exception $e) {
            $results['errors'][] = [
                'type' => 'file',
                'error' => $e->getMessage(),
            ];
        }

        return $results;
    }

    /**
     * Map CSV headers to database columns
     * 
     * @param array $headers
     * @return array
     */
    private function mapHeaders($headers)
    {
        $mapping = [
            'question' => null,
            'option_a' => null,
            'option_b' => null,
            'option_c' => null,
            'option_d' => null,
            'correct_answer' => null,
            'difficulty' => null,
            'explanation' => null,
        ];

        foreach ($headers as $index => $header) {
            $header = strtolower(trim($header));

            if (isset($mapping[$header])) {
                $mapping[$header] = $index;
            }
        }

        return $mapping;
    }

    /**
     * Parse a single MCQ row from CSV
     * 
     * @param array $data
     * @param array $headerMap
     * @param int $subjectId
     * @param int $userId
     * @return array
     */
    private function parseMcqRow($data, $headerMap, $subjectId, $userId)
    {
        // Check required fields
        foreach (['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer'] as $required) {
            if ($headerMap[$required] === null) {
                throw new Exception("Missing required column: $required");
            }
        }

        $question = trim($data[$headerMap['question']] ?? '');
        $optionA = trim($data[$headerMap['option_a']] ?? '');
        $optionB = trim($data[$headerMap['option_b']] ?? '');
        $optionC = trim($data[$headerMap['option_c']] ?? '');
        $optionD = trim($data[$headerMap['option_d']] ?? '');
        $correctAnswer = strtoupper(trim($data[$headerMap['correct_answer']] ?? ''));
        $difficulty = $headerMap['difficulty'] !== null 
            ? strtolower(trim($data[$headerMap['difficulty']] ?? 'medium'))
            : 'medium';
        $explanation = $headerMap['explanation'] !== null
            ? trim($data[$headerMap['explanation']] ?? '')
            : null;

        // Validate
        if (empty($question) || empty($optionA) || empty($optionB) || empty($optionC) || empty($optionD)) {
            throw new Exception('Question or options cannot be empty');
        }

        if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
            throw new Exception('Correct answer must be A, B, C, or D');
        }

        if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
            $difficulty = 'medium';
        }

        return [
            'subject_id' => $subjectId,
            'created_by' => $userId,
            'question' => $question,
            'option_a' => $optionA,
            'option_b' => $optionB,
            'option_c' => $optionC,
            'option_d' => $optionD,
            'correct_answer' => $correctAnswer,
            'difficulty' => $difficulty,
            'explanation' => $explanation,
            'status' => 'pending_review',
        ];
    }

    /**
     * Get CSV template content
     * 
     * @return string
     */
    public function getCsvTemplate()
    {
        $headers = [
            'Question',
            'Option A',
            'Option B',
            'Option C',
            'Option D',
            'Correct Answer',
            'Difficulty',
            'Explanation',
        ];

        $sample = [
            [
                'What is the capital of France?',
                'London',
                'Paris',
                'Berlin',
                'Madrid',
                'B',
                'easy',
                'Paris is the capital and largest city of France.',
            ],
        ];

        $output = implode(',', $headers) . "\n";

        foreach ($sample as $row) {
            $output .= implode(',', array_map(fn($cell) => '"' . addslashes($cell) . '"', $row)) . "\n";
        }

        return $output;
    }
}