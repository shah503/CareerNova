<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'user_id',
        'mcq_id',
        'selected_answer',
        'correct_answer',
        'is_correct',
        'time_taken_seconds',
        'question_order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    // Relationships
    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mcq()
    {
        return $this->belongsTo(Mcq::class);
    }

    // Scopes
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    public function scopeUnanswered($query)
    {
        return $query->whereNull('selected_answer');
    }

    public function scopeBySession($query, $sessionId)
    {
        return $query->where('exam_session_id', $sessionId);
    }

    // Accessors
    public function getStatusBadge()
    {
        if ($this->selected_answer === null) {
            return '<span class="badge bg-secondary">Unanswered</span>';
        }

        return $this->is_correct
            ? '<span class="badge bg-success">Correct</span>'
            : '<span class="badge bg-danger">Incorrect</span>';
    }

    public function getTimeTakenFormatted()
    {
        $seconds = $this->time_taken_seconds;
        $minutes = intval($seconds / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d', $minutes, $secs);
    }
}