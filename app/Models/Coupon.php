<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_percent',
        'expiry_date',
        'usage_limit',
        'times_used',
        'status',
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'active')
            ->where('expiry_date', '>', now());
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    // Methods
    public function isValid()
    {
        return $this->status === 'active' && $this->expiry_date > now();
    }

    public function isExpired()
    {
        return $this->expiry_date <= now();
    }

    public function canBeUsed()
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function getDiscountAmount($amount)
    {
        return ($amount * $this->discount_percent) / 100;
    }

    public function getDiscountedAmount($amount)
    {
        return $amount - $this->getDiscountAmount($amount);
    }

    public function incrementUsage()
    {
        $this->increment('times_used');
    }
}