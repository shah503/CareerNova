<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamAnalytic extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exam_analytics';

    protected $fillable = [
        'user_id',
        'subject_id',
        'total_tests',
        'average_score',
        'tests_passed',
        'tests_failed',
        'total_questions_attempted',
        'total_correct_answers',
        'current_rank',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function getAccuracyPercentageAttribute()
    {
        if ($this->total_questions_attempted === 0) {
            return 0;
        }
        return ($this->total_correct_answers / $this->total_questions_attempted) * 100;
    }
}