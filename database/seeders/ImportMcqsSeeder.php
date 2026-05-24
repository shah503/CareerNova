<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mcq;
use App\Models\User;

class ImportMcqsSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n🚀 Importing MCQs...\n";

        $teacher = User::where('email', 'teacher@careernova.com')->first();
        $admin = User::where('email', 'admin@careernova.com')->first();

        if (!$teacher || !$admin) {
            echo "❌ Teacher or Admin not found. Run CareerNovaSeeder first.\n";
            return;
        }

        // Sample MCQs in your format
        $mcqs = [
            [
                'mcq_uid' => 'MCQ-MDCAT-2024-001',
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'discipline' => 'Medical Sciences',
                'subject' => 'Biology',
                'chapter' => 'Chapter 1: Cell Structure',
                'topic' => 'Cell Organelles',
                'subtopic' => 'Mitochondria',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Remember',
                'question_type' => 'Multiple Choice',
                'source_book' => 'Biology Textbook',
                'source_page' => '15',
                'year' => 2024,
                'exam' => 'MDCAT',
                'paper_code' => 'MDCAT-2024-A',
                'question' => 'Which organelle is known as the powerhouse of the cell?',
                'option_a' => 'Ribosome',
                'option_b' => 'Mitochondria',
                'option_c' => 'Nucleus',
                'option_d' => 'Vacuole',
                'correct_option' => 'B',
                'explanation' => 'Mitochondria produces ATP through cellular respiration, providing energy for cell functions.',
                'keywords_tags' => ['mitochondria', 'energy', 'ATP', 'respiration'],
                'ai_generated' => false,
                'verified' => true,
                'verified_by' => $admin->id,
                'status' => 'active',
                'confidence_score' => 0.95,
            ],
            [
                'mcq_uid' => 'MCQ-MDCAT-2024-002',
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'discipline' => 'Medical Sciences',
                'subject' => 'Chemistry',
                'chapter' => 'Chapter 1: Atomic Structure',
                'topic' => 'Atomic Number',
                'subtopic' => 'Electrons and Protons',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Remember',
                'question_type' => 'Multiple Choice',
                'source_book' => 'Chemistry Textbook',
                'source_page' => '20',
                'year' => 2024,
                'exam' => 'MDCAT',
                'paper_code' => 'MDCAT-2024-A',
                'question' => 'What is the atomic number of Oxygen?',
                'option_a' => '6',
                'option_b' => '8',
                'option_c' => '10',
                'option_d' => '12',
                'correct_option' => 'B',
                'explanation' => 'Oxygen has 8 protons and 8 electrons. Atomic number equals the number of protons.',
                'keywords_tags' => ['oxygen', 'atomic number', 'protons'],
                'ai_generated' => false,
                'verified' => true,
                'verified_by' => $admin->id,
                'status' => 'active',
                'confidence_score' => 0.95,
            ],
        ];

        foreach ($mcqs as $mcq) {
            Mcq::create($mcq);
            echo "✓ Created: {$mcq['mcq_uid']}\n";
        }

        echo "✅ MCQs imported successfully!\n\n";
    }
}