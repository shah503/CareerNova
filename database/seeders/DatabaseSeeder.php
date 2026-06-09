<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            SubjectSeeder::class,
            UserSeeder::class,
            McqSeeder::class,
            ExamSessionSeeder::class,
        ]);

        echo "\n✅ Database seeding completed successfully!\n";
    }
}