<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'start_date',
        'end_date',
        'is_auto_renewal',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_auto_renewal' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('end_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')->orWhere('end_date', '<=', now());
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function isActive()
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    public function isExpired()
    {
        return $this->status === 'expired' || $this->end_date <= now();
    }

    public function getDaysRemaining()
    {
        if (!$this->end_date) {
            return null;
        }

        return now()->diffInDays($this->end_date);
    }
}