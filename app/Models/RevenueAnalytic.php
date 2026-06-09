<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RevenueAnalytic extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'revenue_analytics';

    protected $fillable = [
        'daily_income',
        'monthly_income',
    ];

    protected $casts = [
        'daily_income' => 'decimal:2',
        'monthly_income' => 'decimal:2',
    ];
}