<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueAnalytic extends Model
{
    use HasFactory;

    protected $table = 'revenue_analytics';

    protected $fillable = [
        'date',
        'daily_income',
        'monthly_income',
        'total_transactions',
        'successful_transactions',
        'failed_transactions',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Scopes
    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeByMonth($query, $month, $year = null)
    {
        $year = $year ?? now()->year;
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('date', $year);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('date', '>=', now()->subDays($days));
    }

    // Methods
    public function getSuccessRate()
    {
        if ($this->total_transactions === 0) {
            return 0;
        }

        return ($this->successful_transactions / $this->total_transactions) * 100;
    }

    public function getFailureRate()
    {
        if ($this->total_transactions === 0) {
            return 0;
        }

        return ($this->failed_transactions / $this->total_transactions) * 100;
    }

    public function getAverageTransaction()
    {
        if ($this->successful_transactions === 0) {
            return 0;
        }

        return $this->daily_income / $this->successful_transactions;
    }
}