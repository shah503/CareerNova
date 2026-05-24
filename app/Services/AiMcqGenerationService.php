<?php

namespace App\Services;

use App\Models\AiMcqGeneration;
use App\Models\Mcq;
use App\Models\McqValidationLog;
use Illuminate\Support\Facades\Http;

class AiMcqGenerationService
{
    /**
     * Generate MCQs using AI
     */
    public static function generateMcqs($adminId, $class, $subject, $book, $chapter, $topic, $difficulty, $count)
    {
        $startTime = microtime(true);

        // Create generation record
        $generation = AiMcqGeneration::create([
            'admin_id' => $adminId,
            'class' => $class,
            'subject' => $subject,
            'book' => $book,
            'chapter' => $chapter,
            'topic' => $topic,
            'difficulty' => $difficulty,
            'count_requested' => $count,
            'status' => 'processing'
        ]);

        try {
            // Call AI service
            $mcqs = self::callAiService($class, $subject, $book, $chapter, $topic, $difficulty, $count);

            if (!$mcqs || count($mcqs) === 0) {
                throw new \Exception('AI service returned no MCQs');
            }

            // Validate each MCQ
            $validatedMcqs = [];
            foreach ($mcqs as $mcq) {
                $validation = McqValidationService::validateStructure($mcq);

                if ($validation['passed']) {
                    $validatedMcqs[] = $mcq;
                }
            }

            // Check duplicates
            $finalMcqs = [];
            foreach ($validatedMcqs as $mcq) {
                if (!McqValidationService::isDuplicate($mcq, $finalMcqs)) {
                    $finalMcqs[] = $mcq;
                }
            }

            // Update generation record
            $processingTime = microtime(true) - $startTime;
            $generation->update([
                'count_generated' => count($finalMcqs),
                'generated_mcqs' => $finalMcqs,
                'status' => 'completed',
                'processing_time' => $processingTime
            ]);

            return [
                'success' => true,
                'generation_id' => $generation->id,
                'mcqs' => $finalMcqs,
                'count' => count($finalMcqs)
            ];

        } catch (\Exception $e) {
            $generation->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Call AI service (OpenAI/Gemini)
     */
    private static function callAiService($class, $subject, $book, $chapter, $topic, $difficulty, $count)
    {
        $apiKey = config('ai.openai_key');

        if (!$apiKey) {
            return self::getLocalMcqTemplate($class, $subject, $book, $chapter, $topic, $difficulty, $count);
        }

        $prompt = self::buildPrompt($class, $subject, $book, $chapter, $topic, $difficulty, $count);

        try {
            $response = Http::timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert MCQ generator for educational assessments. Generate high-quality MCQs with correct answers and explanations. Return JSON array.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ], [
                'Authorization' => 'Bearer ' . $apiKey,
            ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'];
                return json_decode($content, true);
            }

            throw new \Exception('AI API failed');

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Build AI prompt
     */
    private static function buildPrompt($class, $subject, $book, $chapter, $topic, $difficulty, $count)
    {
        return "Generate $count MCQs for $class level $subject class.
        
Details:
- Book: $book
- Chapter: $chapter
- Topic: $topic
- Difficulty: $difficulty

Requirements:
1. Each MCQ must have:
   - question (text)
   - option_a (text)
   - option_b (text)
   - option_c (text)
   - option_d (text)
   - correct_option (A/B/C/D)
   - explanation (text)

2. Questions must be:
   - Academically accurate
   - Clear and unambiguous
   - Varied in cognitive levels
   - Age-appropriate for $class

3. Return as JSON array

Format: [{\"question\": \"...\", \"option_a\": \"...\", \"option_b\": \"...\", \"option_c\": \"...\", \"option_d\": \"...\", \"correct_option\": \"A\", \"explanation\": \"...\"}]";
    }

    /**
     * Local template MCQs (when AI unavailable)
     */
    private static function getLocalMcqTemplate($class, $subject, $book, $chapter, $topic, $difficulty, $count)
    {
        return [
            [
                'question' => "What is the definition of $topic?",
                'option_a' => 'Option A text',
                'option_b' => 'Option B text',
                'option_c' => 'Option C text',
                'option_d' => 'Option D text',
                'correct_option' => 'A',
                'explanation' => 'This is a template MCQ. Replace with AI-generated content.',
                'cognitive_level' => 'remember'
            ]
        ];
    }
}