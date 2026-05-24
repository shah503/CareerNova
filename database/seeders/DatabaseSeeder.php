<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mcq;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;

//class CareerNovaSeeder extends Seeder
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create demo users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@careernova.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $teacher = User::create([
            'name' => 'Dr. Hassan Ahmed',
            'email' => 'teacher@careernova.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => "Student $i",
                'email' => "student$i@careernova.com",
                'password' => Hash::make('password'),
                'role' => 'student',
                'batch' => 'MDCAT 2024',
            ]);
        }

        User::create([
            'name' => 'Parent Demo',
            'email' => 'parent@careernova.com',
            'password' => Hash::make('password'),
            'role' => 'parent',
        ]);

        // Create sample verified MCQs
        $mcqData = [
            [
                'subject' => 'Biology',
                'question' => 'Which organelle is known as the powerhouse of the cell?',
                'option_a' => 'Ribosome',
                'option_b' => 'Mitochondria',
                'option_c' => 'Nucleus',
                'option_d' => 'Vacuole',
                'correct_option' => 'B',
                'difficulty' => 'Easy',
                'explanation' => 'Mitochondria produces ATP through cellular respiration.'
            ],
            [
                'subject' => 'Chemistry',
                'question' => 'What is the atomic number of Oxygen?',
                'option_a' => '6',
                'option_b' => '8',
                'option_c' => '10',
                'option_d' => '12',
                'correct_option' => 'B',
                'difficulty' => 'Easy',
                'explanation' => 'Oxygen has 8 protons and 8 electrons.'
            ],
            [
                'subject' => 'Physics',
                'question' => 'What is the SI unit of velocity?',
                'option_a' => 'km/h',
                'option_b' => 'm/s',
                'option_c' => 'cm/s',
                'option_d' => 'mph',
                'correct_option' => 'B',
                'difficulty' => 'Easy',
                'explanation' => 'The SI unit for velocity is meters per second.'
            ],
        ];

        foreach ($mcqData as $mcq) {
            Mcq::create([
                'teacher_id' => $teacher->id,
                'subject' => $mcq['subject'],
                'chapter' => 'Chapter 1',
                'question' => $mcq['question'],
                'option_a' => $mcq['option_a'],
                'option_b' => $mcq['option_b'],
                'option_c' => $mcq['option_c'],
                'option_d' => $mcq['option_d'],
                'correct_option' => $mcq['correct_option'],
                'difficulty' => $mcq['difficulty'],
                'explanation' => $mcq['explanation'],
                'ai_generated' => false,
                'verified' => true,
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'confidence_score' => 0.95
            ]);
        }

        // Initialize system modules
        $modules = [
            'chatbot' => 'Shahjee AI Chatbot',
            'ads' => 'Display Advertisements',
            'reporting' => 'Student MCQ Reporting',
            'ai_generation' => 'AI MCQ Generation',
            'leaderboards' => 'Leaderboard System',
            'registration' => 'User Registration',
            'timed_tests' => 'Timed Exam Sessions',
            'study_groups' => 'Study Groups',
            'analytics' => 'Performance Analytics',
            'notifications' => 'Email Notifications',
        ];

        foreach ($modules as $name => $description) {
            SystemSetting::create([
                'module_name' => $name,
                'description' => $description,
                'enabled' => true,
            ]);
        }

        echo "✅ Seeding complete!\n";
    }
}