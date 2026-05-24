<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mcq;
use App\Models\SystemSetting;
use App\Models\StudentPoints;
use Illuminate\Support\Facades\Hash;

class CareerNovaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🚀 Starting CareerNova Seeding...\n\n";

        // ========================
        // CREATE ADMIN USER
        // ========================
        echo "👤 Creating Admin user...\n";
        
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@careernova.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'batch' => null,
            'total_points' => 0,
            'average_score' => 0,
            'total_tests' => 0,
            'phone' => null
        ]);

        echo "✅ Admin created: admin@careernova.com\n\n";

        // ========================
        // CREATE TEACHER USER
        // ========================
        echo "👨‍🏫 Creating Teacher user...\n";
        
        $teacher = User::create([
            'name' => 'Dr. Hassan Ahmed',
            'email' => 'teacher@careernova.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'batch' => 'MDCAT 2024',
            'total_points' => 0,
            'average_score' => 0,
            'total_tests' => 0,
            'phone' => '03001234567'
        ]);

        echo "✅ Teacher created: teacher@careernova.com\n\n";

        // ========================
        // CREATE STUDENT USERS
        // ========================
        echo "🧑‍🎓 Creating Student users...\n";
        
        $students = [];
        for ($i = 1; $i <= 5; $i++) {
            $student = User::create([
                'name' => "Student $i",
                'email' => "student$i@careernova.com",
                'password' => Hash::make('password'),
                'role' => 'student',
                'batch' => 'MDCAT 2024',
                'total_points' => 0,
                'average_score' => 0,
                'total_tests' => 0,
                'phone' => "0300123456$i"
            ]);
            
            $students[] = $student;
            
            // Create StudentPoints record
            StudentPoints::create([
                'user_id' => $student->id,
                'points' => 0,
                'badge' => null,
                'total_tests' => 0,
                'average_score' => 0
            ]);
            
            echo "   ✓ Student $i created\n";
        }

        echo "✅ All students created\n\n";

        // ========================
        // CREATE PARENT USER
        // ========================
        echo "👨‍👩‍👧 Creating Parent user...\n";
        
        $parent = User::create([
            'name' => 'Parent Demo',
            'email' => 'parent@careernova.com',
            'password' => Hash::make('password'),
            'role' => 'parent',
            'batch' => null,
            'total_points' => 0,
            'average_score' => 0,
            'total_tests' => 0,
            'phone' => '03009876543'
        ]);

        echo "✅ Parent created: parent@careernova.com\n\n";

        // ========================
        // CREATE SAMPLE MCQs
        // ========================
        echo "📝 Creating sample MCQs...\n";

        $mcqsData = [
            // BIOLOGY - Easy
            [
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'subject' => 'Biology',
                'book' => 'Biology Textbook',
                'chapter' => 'Chapter 1: Cell Structure',
                'topic' => 'Cell Organelles',
                'question' => 'Which organelle is known as the powerhouse of the cell?',
                'option_a' => 'Ribosome',
                'option_b' => 'Mitochondria',
                'option_c' => 'Nucleus',
                'option_d' => 'Vacuole',
                'correct_option' => 'B',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Remember',
                'explanation' => 'Mitochondria is responsible for energy production through ATP synthesis during cellular respiration.',
                'ai_generated' => false,
                'verified' => true,
                'confidence_score' => 0.95,
                'approved_by' => $admin->id,
                'approved_at' => now()
            ],
            [
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'subject' => 'Biology',
                'book' => 'Biology Textbook',
                'chapter' => 'Chapter 1: Cell Structure',
                'topic' => 'Endoplasmic Reticulum',
                'question' => 'What is the main function of the endoplasmic reticulum?',
                'option_a' => 'Protein synthesis and lipid synthesis',
                'option_b' => 'DNA replication',
                'option_c' => 'Energy production',
                'option_d' => 'Waste removal',
                'correct_option' => 'A',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Remember',
                'explanation' => 'The endoplasmic reticulum exists in two forms: rough ER (protein synthesis) and smooth ER (lipid synthesis).',
                'ai_generated' => false,
                'verified' => true,
                'confidence_score' => 0.95,
                'approved_by' => $admin->id,
                'approved_at' => now()
            ],
            [
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'subject' => 'Biology',
                'book' => 'Biology Textbook',
                'chapter' => 'Chapter 2: Photosynthesis',
                'topic' => 'Photosynthesis Process',
                'question' => 'Photosynthesis primarily occurs in which part of the plant cell?',
                'option_a' => 'Mitochondria',
                'option_b' => 'Chloroplast',
                'option_c' => 'Nucleus',
                'option_d' => 'Ribosome',
                'correct_option' => 'B',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Remember',
                'explanation' => 'Photosynthesis takes place in chloroplasts, which contain chlorophyll for light absorption.',
                'ai_generated' => false,
                'verified' => true,
                'confidence_score' => 0.95,
                'approved_by' => $admin->id,
                'approved_at' => now()
            ],

            // CHEMISTRY - Easy
            [
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'subject' => 'Chemistry',
                'book' => 'Chemistry Textbook',
                'chapter' => 'Chapter 1: Atomic Structure',
                'topic' => 'Atomic Number',
                'question' => 'What is the atomic number of Oxygen?',
                'option_a' => '6',
                'option_b' => '8',
                'option_c' => '10',
                'option_d' => '12',
                'correct_option' => 'B',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Remember',
                'explanation' => 'Oxygen has 8 protons and 8 electrons. Atomic number equals the number of protons.',
                'ai_generated' => false,
                'verified' => true,
                'confidence_score' => 0.95,
                'approved_by' => $admin->id,
                'approved_at' => now()
            ],
            [
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'subject' => 'Chemistry',
                'book' => 'Chemistry Textbook',
                'chapter' => 'Chapter 1: Atomic Structure',
                'topic' => 'Valence Electrons',
                'question' => 'How many valence electrons does oxygen have?',
                'option_a' => '4',
                'option_b' => '5',
                'option_c' => '6',
                'option_d' => '7',
                'correct_option' => 'C',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Remember',
                'explanation' => 'Oxygen has electron configuration 2,6. It has 6 valence electrons in its outer shell.',
                'ai_generated' => false,
                'verified' => true,
                'confidence_score' => 0.95,
                'approved_by' => $admin->id,
                'approved_at' => now()
            ],
            [
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'subject' => 'Chemistry',
                'book' => 'Chemistry Textbook',
                'chapter' => 'Chapter 2: Chemical Bonding',
                'topic' => 'Ionic Bonds',
                'question' => 'Which type of bond forms between sodium and chlorine?',
                'option_a' => 'Covalent bond',
                'option_b' => 'Ionic bond',
                'option_c' => 'Hydrogen bond',
                'option_d' => 'Metallic bond',
                'correct_option' => 'B',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Understand',
                'explanation' => 'Sodium and chlorine form an ionic bond through electron transfer, creating NaCl (table salt).',
                'ai_generated' => false,
                'verified' => true,
                'confidence_score' => 0.95,
                'approved_by' => $admin->id,
                'approved_at' => now()
            ],

            // PHYSICS - Easy
            [
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'subject' => 'Physics',
                'book' => 'Physics Textbook',
                'chapter' => 'Chapter 1: Motion',
                'topic' => 'Velocity Units',
                'question' => 'What is the SI unit of velocity?',
                'option_a' => 'km/h',
                'option_b' => 'm/s',
                'option_c' => 'cm/s',
                'option_d' => 'ft/s',
                'correct_option' => 'B',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Remember',
                'explanation' => 'The SI unit for velocity is meters per second (m/s).',
                'ai_generated' => false,
                'verified' => true,
                'confidence_score' => 0.95,
                'approved_by' => $admin->id,
                'approved_at' => now()
            ],
            [
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'subject' => 'Physics',
                'book' => 'Physics Textbook',
                'chapter' => 'Chapter 1: Motion',
                'topic' => 'Acceleration',
                'question' => 'What is acceleration?',
                'option_a' => 'Rate of change of displacement',
                'option_b' => 'Rate of change of velocity',
                'option_c' => 'Rate of change of speed',
                'option_d' => 'Rate of change of distance',
                'correct_option' => 'B',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Understand',
                'explanation' => 'Acceleration is defined as the rate of change of velocity with respect to time.',
                'ai_generated' => false,
                'verified' => true,
                'confidence_score' => 0.95,
                'approved_by' => $admin->id,
                'approved_at' => now()
            ],
            [
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'subject' => 'Physics',
                'book' => 'Physics Textbook',
                'chapter' => 'Chapter 2: Forces',
                'topic' => 'Newtons First Law',
                'question' => 'Newton\'s first law of motion states that:',
                'option_a' => 'Force equals mass times acceleration',
                'option_b' => 'Every action has an equal and opposite reaction',
                'option_c' => 'An object at rest stays at rest unless acted upon by a force',
                'option_d' => 'Energy is always conserved',
                'correct_option' => 'C',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Remember',
                'explanation' => 'Newton\'s first law (Law of Inertia) states that an object continues in its state of rest or uniform motion unless acted upon by a net force.',
                'ai_generated' => false,
                'verified' => true,
                'confidence_score' => 0.95,
                'approved_by' => $admin->id,
                'approved_at' => now()
            ],

            // ENGLISH - Easy
            [
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'subject' => 'English',
                'book' => 'English Grammar',
                'chapter' => 'Chapter 1: Parts of Speech',
                'topic' => 'Nouns',
                'question' => 'Which word is a noun in the sentence: "The cat jumped over the fence"?',
                'option_a' => 'jumped',
                'option_b' => 'over',
                'option_c' => 'cat',
                'option_d' => 'the',
                'correct_option' => 'C',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Remember',
                'explanation' => '"Cat" and "fence" are nouns. Nouns are words that represent people, places, things, or ideas.',
                'ai_generated' => false,
                'verified' => true,
                'confidence_score' => 0.95,
                'approved_by' => $admin->id,
                'approved_at' => now()
            ],
            [
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'subject' => 'English',
                'book' => 'English Grammar',
                'chapter' => 'Chapter 1: Parts of Speech',
                'topic' => 'Verbs',
                'question' => 'Which word is a verb in the sentence: "She reads a book every day"?',
                'option_a' => 'She',
                'option_b' => 'reads',
                'option_c' => 'book',
                'option_d' => 'day',
                'correct_option' => 'B',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Remember',
                'explanation' => '"Reads" is the verb as it describes the action. Verbs are action words.',
                'ai_generated' => false,
                'verified' => true,
                'confidence_score' => 0.95,
                'approved_by' => $admin->id,
                'approved_at' => now()
            ],

            // ANALYTICAL REASONING - Easy
            [
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'subject' => 'Analytical Reasoning',
                'book' => 'Logic & Reasoning',
                'chapter' => 'Chapter 1: Basic Logic',
                'topic' => 'Analogies',
                'question' => 'Cat is to kitten as dog is to:',
                'option_a' => 'puppy',
                'option_b' => 'pup',
                'option_c' => 'pet',
                'option_d' => 'animal',
                'correct_option' => 'A',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Understand',
                'explanation' => 'Just as a kitten is a young cat, a puppy is a young dog. The analogy shows parent-child relationship.',
                'ai_generated' => false,
                'verified' => true,
                'confidence_score' => 0.95,
                'approved_by' => $admin->id,
                'approved_at' => now()
            ],
            [
                'teacher_id' => $teacher->id,
                'class' => '1st Year',
                'subject' => 'Analytical Reasoning',
                'book' => 'Logic & Reasoning',
                'chapter' => 'Chapter 1: Basic Logic',
                'topic' => 'Series Completion',
                'question' => 'Complete the series: 2, 4, 8, 16, ?',
                'option_a' => '24',
                'option_b' => '32',
                'option_c' => '36',
                'option_d' => '48',
                'correct_option' => 'B',
                'difficulty' => 'Easy',
                'cognitive_level' => 'Understand',
                'explanation' => 'Each number is multiplied by 2. So 16 × 2 = 32. The pattern is a geometric sequence with ratio 2.',
                'ai_generated' => false,
                'verified' => true,
                'confidence_score' => 0.95,
                'approved_by' => $admin->id,
                'approved_at' => now()
            ],
        ];

        // Insert MCQs
        foreach ($mcqsData as $mcqData) {
            Mcq::create($mcqData);
        }

        echo "✅ Created " . count($mcqsData) . " sample MCQs\n\n";

        // ========================
        // INITIALIZE SYSTEM SETTINGS
        // ========================
        echo "⚙️  Initializing system modules...\n";

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
                'configuration' => null,
                'updated_by' => $admin->id,
                'updated_at' => now()
            ]);
            
            echo "   ✓ Module '$name' enabled\n";
        }

        echo "✅ All system modules initialized\n\n";

        // ========================
        // SUMMARY
        // ========================
        echo "\n╔════════════════════════════════════════════════════╗\n";
        echo "║        ✅ CAREERNOVA SEEDING COMPLETED            ║\n";
        echo "╚════════════════════════════════════════════════════╝\n\n";

        echo "📊 Created:\n";
        echo "   ✓ 1 Admin Account\n";
        echo "   ✓ 1 Teacher Account\n";
        echo "   ✓ 5 Student Accounts\n";
        echo "   ✓ 1 Parent Account\n";
        echo "   ✓ 13 Sample MCQs\n";
        echo "   ✓ 10 System Modules\n\n";

        echo "📧 Demo Login Credentials:\n";
        echo "   Admin:    admin@careernova.com / password\n";
        echo "   Teacher:  teacher@careernova.com / password\n";
        echo "   Student:  student1@careernova.com / password\n";
        echo "   Parent:   parent@careernova.com / password\n\n";

        echo "🚀 CareerNova is ready to use!\n";
        echo "📍 Visit: http://localhost:8000\n\n";
    }
}