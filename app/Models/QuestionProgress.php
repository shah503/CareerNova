<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'question_number',
        'status',
        'selected_answer',
        'last_visited_at',
    ];

    protected $casts = [
        'last_visited_at' => 'datetime',
    ];

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'not_visited' => 'gray',
            'visited' => 'yellow',
            'answered' => 'blue',
            'marked' => 'purple',
            'answered_marked' => 'green',
            default => 'gray'
        };
    }
}