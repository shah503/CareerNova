<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Subject;

class CompleteSetupSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n✅ Creating users...\n";

        // 1. Admin
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // 2. Student
        User::firstOrCreate(
            ['email' => 'student@test.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'status' => 'active',
            ]
        );

        // 🚀 ADDED: 3. Teacher
        User::firstOrCreate(
            ['email' => 'teacher@test.com'],
            [
                'name' => 'Teacher User',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'status' => 'active',
            ]
        );

        // 🚀 ADDED: 4. Parent
        User::firstOrCreate(
            ['email' => 'parent@test.com'],
            [
                'name' => 'Parent User',
                'password' => Hash::make('password123'),
                'role' => 'parent',
                'status' => 'active',
            ]
        );

        echo "✅ All 4 Role Users created!\n";

        echo "\n📚 Creating subjects...\n";

        $subjects = [
            ['name' => 'Biology', 'description' => 'Biology subject'],
            ['name' => 'Chemistry', 'description' => 'Chemistry subject'],
            ['name' => 'Physics', 'description' => 'Physics subject'],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['name' => $subject['name']],
                [
                    'name' => $subject['name'],
                    'slug' => \Illuminate\Support\Str::slug($subject['name']),
                    'description' => $subject['description'],
                    'status' => 'active',
                ]
            );
        }

        echo "✅ Subjects created!\n";
        echo "✅ Setup complete!\n";
    }
}