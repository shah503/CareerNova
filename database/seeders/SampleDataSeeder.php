<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Mcq;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create subjects
        $subjects = [
            ['name' => 'Biology', 'description' => 'Human body and cells', 'code' => 'BIO101'],
            ['name' => 'Chemistry', 'description' => 'Chemical reactions', 'code' => 'CHM101'],
            ['name' => 'Physics', 'description' => 'Motion and energy', 'code' => 'PHY101'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        // Create sample MCQs
        $biology = Subject::where('code', 'BIO101')->first();

        $mcqs = [
            [
                'subject_id' => $biology->id,
                'created_by' => 2, // Teacher
                'question' => 'What is the basic unit of life?',
                'option_a' => 'Atom',
                'option_b' => 'Cell',
                'option_c' => 'Molecule',
                'option_d' => 'Tissue',
                'correct_answer' => 'B',
                'difficulty' => 'easy',
                'explanation' => 'The cell is the basic unit of life.',
                'status' => 'active',
            ],
            [
                'subject_id' => $biology->id,
                'created_by' => 2,
                'question' => 'Photosynthesis occurs in which organelle?',
                'option_a' => 'Mitochondria',
                'option_b' => 'Ribosome',
                'option_c' => 'Chloroplast',
                'option_d' => 'Nucleus',
                'correct_answer' => 'C',
                'difficulty' => 'medium',
                'explanation' => 'Chloroplasts are where photosynthesis takes place.',
                'status' => 'active',
            ],
        ];

        foreach ($mcqs as $mcq) {
            Mcq::create($mcq);
        }

        echo "Sample data created successfully!\n";
    }
}