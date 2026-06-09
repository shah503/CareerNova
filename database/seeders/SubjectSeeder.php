<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use Illuminate\Support\Str;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Biology',
                'code' => 'BIO101',
                'slug' => 'biology',
                'description' => 'Study of living organisms, cells, genetics, and human anatomy',
            ],
            [
                'name' => 'Chemistry',
                'code' => 'CHM101',
                'slug' => 'chemistry',
                'description' => 'Chemical reactions, periodic table, organic and inorganic chemistry',
            ],
            [
                'name' => 'Physics',
                'code' => 'PHY101',
                'slug' => 'physics',
                'description' => 'Motion, forces, energy, waves, and modern physics',
            ],
            [
                'name' => 'English',
                'code' => 'ENG101',
                'slug' => 'english',
                'description' => 'Grammar, vocabulary, comprehension, and writing skills',
            ],
            [
                'name' => 'Islamic Studies',
                'code' => 'ISL101',
                'slug' => 'islamic-studies',
                'description' => 'Quranic studies, Hadith, Islamic history and jurisprudence',
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        echo "✅ Created " . count($subjects) . " subjects\n";
    }
}