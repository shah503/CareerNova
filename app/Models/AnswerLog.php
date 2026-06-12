<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnswerLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'mcq_id',
        'exam_session_id',
        'answer',
        'selected_answer',
        'correct_answer',
        'is_correct',
        'is_review',
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
        'is_correct' => 'boolean',
        'is_review' => 'boolean',
    ];

    // ✅ FIXED: Correct relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mcq()
    {
        return $this->belongsTo(Mcq::class);
    }

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }
}