<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mcq;
use App\Models\Subject;

class McqSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = Subject::all();
        $teacherId = 2; // Assuming teacher is ID 2
        $count = 0;

        // Biology MCQs
        $biology = $subjects->where('code', 'BIO101')->first();
        if ($biology) {
            $biologyMcqs = [
                [
                    'question' => 'What is the basic unit of life?',
                    'option_a' => 'Atom',
                    'option_b' => 'Cell',
                    'option_c' => 'Molecule',
                    'option_d' => 'Tissue',
                    'correct_answer' => 'B',
                    'difficulty' => 'easy',
                    'explanation' => 'The cell is the smallest unit of life capable of independent function.',
                ],
                [
                    'question' => 'Which organelle is responsible for photosynthesis?',
                    'option_a' => 'Mitochondria',
                    'option_b' => 'Ribosome',
                    'option_c' => 'Chloroplast',
                    'option_d' => 'Nucleus',
                    'correct_answer' => 'C',
                    'difficulty' => 'easy',
                    'explanation' => 'Chloroplasts are where photosynthesis occurs in plant cells.',
                ],
                [
                    'question' => 'What is the process by which cells divide?',
                    'option_a' => 'Transcription',
                    'option_b' => 'Mitosis',
                    'option_c' => 'Photosynthesis',
                    'option_d' => 'Respiration',
                    'correct_answer' => 'B',
                    'difficulty' => 'medium',
                    'explanation' => 'Mitosis is the process of cell division that produces two identical daughter cells.',
                ],
                [
                    'question' => 'Which blood cells are responsible for carrying oxygen?',
                    'option_a' => 'White blood cells',
                    'option_b' => 'Red blood cells',
                    'option_c' => 'Platelets',
                    'option_d' => 'Plasma cells',
                    'correct_answer' => 'B',
                    'difficulty' => 'easy',
                    'explanation' => 'Red blood cells contain hemoglobin which binds to oxygen.',
                ],
                [
                    'question' => 'What is the function of the mitochondria?',
                    'option_a' => 'Protein synthesis',
                    'option_b' => 'Energy production',
                    'option_c' => 'Photosynthesis',
                    'option_d' => 'DNA storage',
                    'correct_answer' => 'B',
                    'difficulty' => 'medium',
                    'explanation' => 'Mitochondria are the powerhouse of the cell, producing ATP for energy.',
                ],
            ];

            foreach ($biologyMcqs as $mcq) {
                Mcq::create([
                    'subject_id' => $biology->id,
                    'created_by' => $teacherId,
                    'question' => $mcq['question'],
                    'option_a' => $mcq['option_a'],
                    'option_b' => $mcq['option_b'],
                    'option_c' => $mcq['option_c'],
                    'option_d' => $mcq['option_d'],
                    'correct_answer' => $mcq['correct_answer'],
                    'difficulty' => $mcq['difficulty'],
                    'explanation' => $mcq['explanation'],
                    'status' => 'active',
                    'verified' => true,
                    'approved_by' => 1, // Admin ID
                    'approved_at' => now(),
                ]);
                $count++;
            }
        }

        // Chemistry MCQs
        $chemistry = $subjects->where('code', 'CHM101')->first();
        if ($chemistry) {
            $chemistryMcqs = [
                [
                    'question' => 'What is the atomic number of Carbon?',
                    'option_a' => '4',
                    'option_b' => '6',
                    'option_c' => '8',
                    'option_d' => '12',
                    'correct_answer' => 'B',
                    'difficulty' => 'easy',
                    'explanation' => 'Carbon has an atomic number of 6, meaning it has 6 protons.',
                ],
                [
                    'question' => 'Which gas is used in photosynthesis?',
                    'option_a' => 'Oxygen',
                    'option_b' => 'Nitrogen',
                    'option_c' => 'Carbon dioxide',
                    'option_d' => 'Hydrogen',
                    'correct_answer' => 'C',
                    'difficulty' => 'easy',
                    'explanation' => 'Plants use CO2 from the atmosphere for photosynthesis.',
                ],
                [
                    'question' => 'What is the pH of a neutral solution?',
                    'option_a' => '0',
                    'option_b' => '7',
                    'option_c' => '14',
                    'option_d' => '10',
                    'correct_answer' => 'B',
                    'difficulty' => 'easy',
                    'explanation' => 'A pH of 7 is neutral on the pH scale (0-14).',
                ],
                [
                    'question' => 'What is the most abundant element in the universe?',
                    'option_a' => 'Oxygen',
                    'option_b' => 'Carbon',
                    'option_c' => 'Hydrogen',
                    'option_d' => 'Helium',
                    'correct_answer' => 'C',
                    'difficulty' => 'medium',
                    'explanation' => 'Hydrogen is the most abundant element in the universe.',
                ],
                [
                    'question' => 'What type of bond is formed between two hydrogen atoms?',
                    'option_a' => 'Ionic bond',
                    'option_b' => 'Covalent bond',
                    'option_c' => 'Metallic bond',
                    'option_d' => 'Hydrogen bond',
                    'correct_answer' => 'B',
                    'difficulty' => 'medium',
                    'explanation' => 'Two hydrogen atoms form a covalent bond by sharing electrons.',
                ],
            ];

            foreach ($chemistryMcqs as $mcq) {
                Mcq::create([
                    'subject_id' => $chemistry->id,
                    'created_by' => $teacherId,
                    'question' => $mcq['question'],
                    'option_a' => $mcq['option_a'],
                    'option_b' => $mcq['option_b'],
                    'option_c' => $mcq['option_c'],
                    'option_d' => $mcq['option_d'],
                    'correct_answer' => $mcq['correct_answer'],
                    'difficulty' => $mcq['difficulty'],
                    'explanation' => $mcq['explanation'],
                    'status' => 'active',
                    'verified' => true,
                    'approved_by' => 1,
                    'approved_at' => now(),
                ]);
                $count++;
            }
        }

        // Physics MCQs
        $physics = $subjects->where('code', 'PHY101')->first();
        if ($physics) {
            $physicsMcqs = [
                [
                    'question' => 'What is the SI unit of force?',
                    'option_a' => 'Joule',
                    'option_b' => 'Watt',
                    'option_c' => 'Newton',
                    'option_d' => 'Pascal',
                    'correct_answer' => 'C',
                    'difficulty' => 'easy',
                    'explanation' => 'Newton (N) is the SI unit of force.',
                ],
                [
                    'question' => 'What is the speed of light?',
                    'option_a' => '3 × 10^8 m/s',
                    'option_b' => '3 × 10^5 m/s',
                    'option_c' => '3 × 10^6 m/s',
                    'option_d' => '3 × 10^10 m/s',
                    'correct_answer' => 'A',
                    'difficulty' => 'easy',
                    'explanation' => 'The speed of light is approximately 3 × 10^8 m/s or 300,000 km/s.',
                ],
                [
                    'question' => 'Newton\'s first law of motion states that:',
                    'option_a' => 'Force equals mass times acceleration',
                    'option_b' => 'An object at rest stays at rest unless acted upon by force',
                    'option_c' => 'For every action there is an equal and opposite reaction',
                    'option_d' => 'Energy cannot be created or destroyed',
                    'correct_answer' => 'B',
                    'difficulty' => 'medium',
                    'explanation' => 'This is Newton\'s law of inertia.',
                ],
                [
                    'question' => 'What is the SI unit of energy?',
                    'option_a' => 'Watt',
                    'option_b' => 'Joule',
                    'option_c' => 'Newton',
                    'option_d' => 'Pascal',
                    'correct_answer' => 'B',
                    'difficulty' => 'easy',
                    'explanation' => 'Joule (J) is the SI unit of energy.',
                ],
                [
                    'question' => 'What is the relationship between velocity and acceleration?',
                    'option_a' => 'Velocity is the derivative of acceleration',
                    'option_b' => 'Acceleration is the derivative of velocity',
                    'option_c' => 'They are the same thing',
                    'option_d' => 'There is no relationship',
                    'correct_answer' => 'B',
                    'difficulty' => 'medium',
                    'explanation' => 'Acceleration is the rate of change of velocity with respect to time.',
                ],
            ];

            foreach ($physicsMcqs as $mcq) {
                Mcq::create([
                    'subject_id' => $physics->id,
                    'created_by' => $teacherId,
                    'question' => $mcq['question'],
                    'option_a' => $mcq['option_a'],
                    'option_b' => $mcq['option_b'],
                    'option_c' => $mcq['option_c'],
                    'option_d' => $mcq['option_d'],
                    'correct_answer' => $mcq['correct_answer'],
                    'difficulty' => $mcq['difficulty'],
                    'explanation' => $mcq['explanation'],
                    'status' => 'active',
                    'verified' => true,
                    'approved_by' => 1,
                    'approved_at' => now(),
                ]);
                $count++;
            }
        }

        echo "✅ Created $count MCQs\n";
    }
}