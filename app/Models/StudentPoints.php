<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPoints extends Model
{
    protected $fillable = [
        'user_id', 'points', 'badge', 'total_tests', 'average_score'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function awardPoints($userId, $score, $totalQuestions)
    {
        $accuracy = ($score / $totalQuestions) * 100;
        $points = 0;
        $badge = null;

        if ($accuracy >= 90) {
            $points = 50;
            $badge = 'top_scorer';
        } elseif ($accuracy >= 70) {
            $points = 30;
            $badge = 'consistent';
        } elseif ($accuracy >= 50) {
            $points = 15;
        } else {
            $points = 5;
        }

        $studentPoints = self::firstOrCreate(['user_id' => $userId]);
        $studentPoints->increment('points', $points);
        $studentPoints->increment('total_tests');
        
        if ($badge) {
            $studentPoints->badge = $badge;
        }

        $studentPoints->save();
        return $studentPoints;
    }
}