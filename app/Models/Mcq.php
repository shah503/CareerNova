<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mcq extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'created_by',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'difficulty',
        'explanation',
        'source',
        'status',
    ];

    // Relationships
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function answerLogs()
    {
        return $this->hasMany(AnswerLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }

    // Accessors
    public function getCorrectOptionText()
    {
        $option = 'option_' . strtolower($this->correct_answer);
        return $this->{$option};
    }

    public function getOptionByLetter($letter)
    {
        $column = 'option_' . strtolower($letter);
        return $this->{$column} ?? null;
    }

    public function getDifficultyBadge()
    {
        return match($this->difficulty) {
            'easy' => '<span class="badge bg-success">Easy</span>',
            'medium' => '<span class="badge bg-warning">Medium</span>',
            'hard' => '<span class="badge bg-danger">Hard</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}