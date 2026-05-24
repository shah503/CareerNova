<?php

namespace App\Http\Controllers;

use App\Services\ShahjeeAIService;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ShahjeeController extends Controller
{
    protected $aiService;

    public function __construct()
    {
        $this->aiService = new ShahjeeAIService();
    }

    /**
     * Chat API endpoint
     */
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userId = auth()->id() ?? null;
        $sessionId = $this->getSessionId($request);

        $aiResponse = $this->aiService->getResponse($validated['message'], $userId);

        $chatMessage = ShahjeeAIService::saveMessage(
            $userId,
            $sessionId,
            $validated['message'],
            $aiResponse['response'],
            $aiResponse['source'],
            $aiResponse['confidence']
        );

        return response()->json([
            'message_id' => $chatMessage->id,
            'response' => $aiResponse['response'],
            'source' => $aiResponse['source']
        ]);
    }

    /**
     * Get suggestions
     */
    public function suggestions()
    {
        $userId = auth()->id();
        $suggestions = $this->aiService->getSuggestions($userId);

        return response()->json(['suggestions' => $suggestions]);
    }

    /**
     * Mark feedback
     */
    public function feedback(Request $request)
    {
        $validated = $request->validate([
            'message_id' => 'required|exists:chat_messages,id',
            'is_helpful' => 'required|boolean'
        ]);

        ChatMessage::find($validated['message_id'])->update([
            'is_helpful' => $validated['is_helpful']
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get session ID for guests
     */
    private function getSessionId($request)
    {
        if (auth()->check()) {
            return null;
        }

        $sessionId = $request->cookie('shahjee_session_id');
        if (!$sessionId) {
            $sessionId = 'guest_' . uniqid();
        }

        return $sessionId;
    }
}