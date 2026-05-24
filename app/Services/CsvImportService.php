<?php

namespace App\Services;

use App\Models\Mcq;
use App\Models\Subject;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CsvImportService
{
    /**
     * Import MCQs from CSV file
     */
    public function import(UploadedFile $file, $subjectId, $userId)
    {
        $results = [
            'successful' => 0,
            'failed' => 0,
            'skipped' => 0,
            'details' => []
        ];

        $subject = Subject::find($subjectId);
        if (!$subject) {
            return $results;
        }

        $fileContent = file_get_contents($file->getRealPath());
        $lines = explode("\n", $fileContent);

        $header = null;
        $rowNumber = 0;

        foreach ($lines as $line) {
            $rowNumber++;
            
            // Skip empty lines
            if (empty(trim($line))) {
                $results['skipped']++;
                continue;
            }

            // Parse header
            if ($rowNumber === 1) {
                $header = str_getcsv($line);
                continue;
            }

            // Parse data row
            $data = str_getcsv($line);
            $row = array_combine($header, $data);

            try {
                // Validate required fields
                if (empty($row['Question']) || empty($row['Correct Answer'])) {
                    $results['failed']++;
                    $results['details'][] = [
                        'row' => $rowNumber,
                        'status' => 'failed',
                        'question' => $row['Question'] ?? 'Unknown',
                        'message' => 'Missing required fields (Question or Correct Answer)'
                    ];
                    continue;
                }

                // Validate options
                $options = [
                    'A' => $row['Option A'] ?? '',
                    'B' => $row['Option B'] ?? '',
                    'C' => $row['Option C'] ?? '',
                    'D' => $row['Option D'] ?? '',
                ];

                $missingOptions = array_filter($options, fn($opt) => empty($opt));
                if (count($missingOptions) > 0) {
                    $results['failed']++;
                    $results['details'][] = [
                        'row' => $rowNumber,
                        'status' => 'failed',
                        'question' => substr($row['Question'], 0, 50),
                        'message' => 'Missing options (A, B, C, or D)'
                    ];
                    continue;
                }

                // Validate correct answer
                $correctAnswer = strtoupper(trim($row['Correct Answer']));
                if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
                    $results['failed']++;
                    $results['details'][] = [
                        'row' => $rowNumber,
                        'status' => 'failed',
                        'question' => substr($row['Question'], 0, 50),
                        'message' => 'Invalid correct answer (must be A, B, C, or D)'
                    ];
                    continue;
                }

                // Clean special characters from all text fields
                $cleanOption = function($text) {
                    // Convert special dashes to regular dash
                    $text = str_replace(['–', '—', '−'], '-', $text);
                    // Remove other problematic characters
                    $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
                    return trim($text);
                };

                // Create MCQ
                Mcq::create([
                    'subject_id' => $subjectId,
                    'question' => $cleanOption($row['Question']),
                    'option_a' => $cleanOption($options['A']),
                    'option_b' => $cleanOption($options['B']),
                    'option_c' => $cleanOption($options['C']),
                    'option_d' => $cleanOption($options['D']),
                    'correct_answer' => $correctAnswer,
                    'difficulty' => in_array(strtolower($row['Difficulty'] ?? 'medium'), ['easy', 'medium', 'hard']) 
                        ? strtolower($row['Difficulty']) 
                        : 'medium',
                    'explanation' => $cleanOption($row['Explanation'] ?? ''),
                    'status' => 'pending',
                    'created_by' => $userId,
                ]);

                $results['successful']++;
                $results['details'][] = [
                    'row' => $rowNumber,
                    'status' => 'success',
                    'question' => substr($row['Question'], 0, 50),
                    'message' => 'Successfully imported'
                ];

            } catch (\Exception $e) {
                $results['failed']++;
                $results['details'][] = [
                    'row' => $rowNumber,
                    'status' => 'failed',
                    'question' => substr($row['Question'] ?? 'Unknown', 0, 50),
                    'message' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Get CSV template
     */
    public function getCsvTemplate()
    {
        $headers = ['Question', 'Option A', 'Option B', 'Option C', 'Option D', 'Correct Answer', 'Difficulty', 'Explanation'];
        
        $sample = [
            ['What is the capital of France?', 'London', 'Paris', 'Berlin', 'Madrid', 'B', 'easy', 'Paris is the capital of France'],
            ['What is 2 + 2?', '3', '4', '5', '6', 'B', 'easy', '2 + 2 = 4'],
        ];

        $csv = implode(',', $headers) . "\n";
        
        foreach ($sample as $row) {
            $csv .= '"' . implode('","', $row) . '"' . "\n";
        }

        return $csv;
    }
}