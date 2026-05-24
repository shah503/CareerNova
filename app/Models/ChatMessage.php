<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'message', 'response',
        'ai_service', 'confidence', 'is_helpful', 'helpful_feedback'
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}