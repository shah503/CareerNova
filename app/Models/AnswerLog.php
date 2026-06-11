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
        'is_review',
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(\App\Models\ExamSession::class);
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