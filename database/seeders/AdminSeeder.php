<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin if doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@careernova.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin123456'), // Change this!
                'role' => 'admin',
                'status' => 'active',
                'batch' => 'Admin',
                'phone' => '+92-300-0000000',
            ]
        );

        echo "✅ Default admin created!\n";
        echo "📧 Email: admin@careernova.com\n";
        echo "🔐 Password: admin123456\n";
        echo "⚠️  IMPORTANT: Change this password immediately!\n";
    }
}