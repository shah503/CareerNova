<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiMcqGeneration extends Model
{
    protected $table = 'ai_mcq_generations';

    protected $fillable = [
        'admin_id', 'class', 'subject', 'book', 'chapter', 'topic',
        'difficulty', 'count_requested', 'count_generated', 'status',
        'generated_mcqs', 'error_message', 'processing_time'
    ];

    protected $casts = [
        'generated_mcqs' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}