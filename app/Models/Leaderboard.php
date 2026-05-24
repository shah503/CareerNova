<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_tests',
        'total_mcqs_answered',
        'total_correct_answers',
        'overall_percentage',
        'overall_rank',
        'weekly_score',
        'monthly_score',
        'weekly_rank',
        'monthly_rank',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeOrderByOverallRank($query)
    {
        return $query->orderBy('overall_rank', 'asc');
    }

    public function scopeOrderByWeeklyRank($query)
    {
        return $query->orderBy('weekly_rank', 'asc');
    }

    public function scopeOrderByMonthlyRank($query)
    {
        return $query->orderBy('monthly_rank', 'asc');
    }

    public function scopeTopStudents($query, $limit = 10)
    {
        return $query->orderBy('overall_rank', 'asc')->limit($limit);
    }

    // Accessors
    public function getSuccessRate()
    {
        if ($this->total_mcqs_answered === 0) {
            return 0;
        }

        return ($this->total_correct_answers / $this->total_mcqs_answered) * 100;
    }
}