<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Mcq;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CsvController extends Controller
{
    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mcq_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'subject_name',
                'question',
                'option_a',
                'option_b',
                'option_c',
                'option_d',
                'correct_answer',
                'difficulty',
                'explanation',
            ]);

            // Example row
            fputcsv($file, [
                'Biology',
                'What is the basic unit of life?',
                'Atom',
                'Cell',
                'Molecule',
                'Tissue',
                'B',
                'easy',
                'The cell is the smallest unit of life.',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import MCQs from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $path = $file->store('uploads/csv', 'local');
        
        $rows = array_map('str_getcsv', file(storage_path('app/' . $path)));
        $header = array_shift($rows);

        $imported = 0;
        $errors = [];
        $rowNum = 2; // Start from 2 (after header)

        foreach ($rows as $row) {
            try {
                if (empty(array_filter($row))) {
                    continue; // Skip empty rows
                }

                // Validate required fields
                if (count($row) < 9) {
                    $errors[] = "Row $rowNum: Missing required columns.";
                    $rowNum++;
                    continue;
                }

                [$subjectName, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $difficulty, $explanation] = $row;

                // Find subject by name
                $subject = Subject::where('name', trim($subjectName))
                    ->orWhere('code', trim($subjectName))
                    ->first();

                if (!$subject) {
                    $errors[] = "Row $rowNum: Subject '$subjectName' not found.";
                    $rowNum++;
                    continue;
                }

                // Validate correct answer
                $correctAnswer = strtoupper(trim($correctAnswer));
                if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
                    $errors[] = "Row $rowNum: Correct answer must be A, B, C, or D.";
                    $rowNum++;
                    continue;
                }

                // Validate difficulty
                $difficulty = strtolower(trim($difficulty));
                if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
                    $errors[] = "Row $rowNum: Difficulty must be easy, medium, or hard.";
                    $rowNum++;
                    continue;
                }

                // Create MCQ
                Mcq::create([
                    'subject_id' => $subject->id,
                    'created_by' => auth()->id(),
                    'question' => trim($question),
                    'option_a' => trim($optionA),
                    'option_b' => trim($optionB),
                    'option_c' => trim($optionC),
                    'option_d' => trim($optionD),
                    'correct_answer' => $correctAnswer,
                    'difficulty' => $difficulty,
                    'explanation' => trim($explanation),
                    'status' => 'pending_review',
                    'verified' => false,
                    'needs_review' => true,
                ]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row $rowNum: " . $e->getMessage();
            }

            $rowNum++;
        }

        // Clean up uploaded file
        Storage::delete($path);

        $message = "✅ Imported $imported MCQs successfully!";
        if (!empty($errors)) {
            $message .= "\n❌ " . count($errors) . " errors found.\n" . implode("\n", array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= "\n... and " . (count($errors) - 5) . " more errors.";
            }
        }

        return redirect()->route('teacher.mcqs')
            ->with('import_message', $message)
            ->with('import_count', $imported)
            ->with('import_errors', $errors);
    }
}