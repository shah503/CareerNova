<?php

namespace App\Services;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Http;

class ShahjeeAIService
{
    protected $apiKey;
    protected $model;
    protected $service;

    public function __construct()
    {
        $this->service = config('shahjee.ai_service', 'local');

        if ($this->service === 'openai') {
            $this->apiKey = env('OPENAI_API_KEY');
            $this->model = 'gpt-3.5-turbo';
        }
    }

    /**
     * Get response with knowledge base fallback
     */
    public function getResponse($userMessage, $userId = null)
    {
        try {
            $kbResponse = $this->searchKnowledgeBase($userMessage);
            if ($kbResponse) {
                return [
                    'response' => $kbResponse,
                    'source' => 'knowledge_base',
                    'confidence' => 0.95
                ];
            }

            if ($this->apiKey && $this->service === 'openai') {
                $aiResponse = $this->callOpenAI($userMessage, $userId);
                return [
                    'response' => $aiResponse,
                    'source' => 'ai',
                    'confidence' => 0.85
                ];
            }

            return $this->getFallbackResponse();

        } catch (\Exception $e) {
            return $this->getFallbackResponse($e->getMessage());
        }
    }

    /**
     * Search knowledge base
     */
    private function searchKnowledgeBase($query)
    {
        $kb = config('shahjee.knowledge_base');
        $query = strtolower($query);

        foreach ($kb['faq'] as $faq) {
            foreach ($faq['keywords'] as $keyword) {
                if (strpos($query, strtolower($keyword)) !== false) {
                    return $faq['answer'];
                }
            }
        }

        foreach ($kb['subjects'] as $subject => $tip) {
            if (strpos($query, strtolower($subject)) !== false) {
                return "📚 **{$subject}**: {$tip}";
            }
        }

        return null;
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI($message, $userId)
    {
        $systemPrompt = $this->getSystemPrompt($userId);

        try {
            $response = Http::timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $message]
                ],
                'max_tokens' => 500,
                'temperature' => 0.7,
            ], [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'];
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get system prompt with context
     */
    private function getSystemPrompt($userId = null)
    {
        $prompt = "You are Shahjee, a friendly AI learning assistant for CareerNova.
        Be encouraging, use emojis, keep responses concise.
        Help with exam prep, study tips, and platform features.";

        if ($userId) {
            $user = \App\Models\User::find($userId);
            if ($user) {
                $prompt .= "\nUser: {$user->name}, Role: " . ucfirst($user->role);
            }
        }

        return $prompt;
    }

    /**
     * Fallback response
     */
    private function getFallbackResponse($error = null)
    {
        return "Hi! I'm Shahjee 🧠. How can I help you with CareerNova today?";
    }

    /**
     * Get suggestions
     */
    public function getSuggestions($userId)
    {
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return ['📝 Take a practice test', '❓ Check FAQs', '👥 Join a study group'];
        }

        $suggestions = [];

        if ($user->average_score < 50) {
            $suggestions[] = "📚 Focus on weak subjects first";
        }

        if ($user->total_tests < 5) {
            $suggestions[] = "🎯 Take more practice tests";
        }

        if ($user->total_tests > 10) {
            $suggestions[] = "🏆 Check your performance insights";
        }

        $kb = config('shahjee.knowledge_base');
        $tips = $kb['tips'] ?? [];
        if ($tips) {
            $suggestions[] = "💡 " . $tips[array_rand($tips)];
        }

        return array_slice($suggestions, 0, 3);
    }

    /**
     * Save message
     */
    public static function saveMessage($userId, $sessionId, $message, $response, $aiService, $confidence)
    {
        return ChatMessage::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'message' => $message,
            'response' => $response,
            'ai_service' => $aiService,
            'confidence' => $confidence
        ]);
    }
}