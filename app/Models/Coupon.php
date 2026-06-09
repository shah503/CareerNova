<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'discount_percent',
        'expiry',
        'status',
    ];

    protected $casts = [
        'expiry' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('expiry', '>=', now());
    }
}