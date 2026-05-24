<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_id',
        'total_questions',
        'correct_answers',
        'wrong_answers',
        'unanswered',
        'percentage',
        'score',
        'duration_minutes',
        'time_taken_minutes',
        'started_at',
        'finished_at',
        'status',
        'is_locked',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function answerLogs()
    {
        return $this->hasMany(AnswerLog::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    // Accessors
    public function getDurationFormatted()
    {
        return sprintf('%02d:%02d', intval($this->duration_minutes / 60), $this->duration_minutes % 60);
    }

    public function getTimeRemainingSeconds()
    {
        if ($this->status === 'completed' || $this->finished_at) {
            return 0;
        }

        $elapsed = now()->diffInSeconds($this->started_at);
        $totalSeconds = $this->duration_minutes * 60;
        $remaining = $totalSeconds - $elapsed;

        return max(0, $remaining);
    }

    public function isExpired()
    {
        return $this->getTimeRemainingSeconds() <= 0;
    }

    public function getResult()
    {
        return [
            'total_questions' => $this->total_questions,
            'correct_answers' => $this->correct_answers,
            'wrong_answers' => $this->wrong_answers,
            'unanswered' => $this->unanswered,
            'percentage' => $this->percentage,
            'score' => $this->score,
        ];
    }
}