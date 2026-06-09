<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mcq extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject_id',
        'created_by',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer', // ✅ FIXED: Use correct_answer to match migration
        'difficulty',
        'explanation',
        'source',
        'status',
        'verified',
        'needs_review',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function scopeVerified($query)
    {
        return $query->where('status', 'active')->where('verified', true);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }
}