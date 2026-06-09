<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Teachers
            [
                'name' => 'Dr. Ahmed Khan',
                'email' => 'ahmed.khan@careernova.com',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'batch' => 'Senior Teacher',
                'phone' => '+92-300-1111111',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Fatima Ali',
                'email' => 'fatima.ali@careernova.com',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'batch' => 'Senior Teacher',
                'phone' => '+92-300-2222222',
                'status' => 'active',
            ],
            // Students
            [
                'name' => 'Ali Ahmed',
                'email' => 'ali.ahmed@student.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'batch' => '2024-A',
                'phone' => '+92-300-3333333',
                'status' => 'active',
            ],
            [
                'name' => 'Zainab Hassan',
                'email' => 'zainab.hassan@student.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'batch' => '2024-A',
                'phone' => '+92-300-4444444',
                'status' => 'active',
            ],
            [
                'name' => 'Hassan Malik',
                'email' => 'hassan.malik@student.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'batch' => '2024-B',
                'phone' => '+92-300-5555555',
                'status' => 'active',
            ],
            [
                'name' => 'Aisha Khan',
                'email' => 'aisha.khan@student.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'batch' => '2024-B',
                'phone' => '+92-300-6666666',
                'status' => 'active',
            ],
            // Parents
            [
                'name' => 'Mr. Rashid Ahmed',
                'email' => 'rashid.ahmed@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'parent',
                'batch' => 'Parent',
                'phone' => '+92-300-7777777',
                'status' => 'active',
            ],
            [
                'name' => 'Mrs. Saira Hassan',
                'email' => 'saira.hassan@parent.com',
                'password' => Hash::make('password123'),
                'role' => 'parent',
                'batch' => 'Parent',
                'phone' => '+92-300-8888888',
                'status' => 'active',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        echo "✅ Created " . count($users) . " test users\n";
    }
}