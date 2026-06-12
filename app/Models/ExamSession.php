<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'mcq_id',
        'subject_id',
        'total_questions',
        'score',
        'correct_answers',
        'wrong_answers',
        'unanswered_count',
        'time_taken_minutes',
        'answers',
        'subject_breakdown',
        'status',
        'percentage',
        'is_passed',
        'is_submitted',
        'started_at',
        'finished_at',
        'completed_at',
        'last_saved_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'subject_breakdown' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_saved_at' => 'datetime',
        'is_submitted' => 'boolean',
        'is_passed' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mcq()
    {
        return $this->belongsTo(Mcq::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function answerLogs()
    {
        return $this->hasMany(AnswerLog::class, 'exam_session_id');
    }
}