<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'price',
        'duration_days',
        'description',
        'status',
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSubscription($query)
    {
        return $query->where('type', 'subscription');
    }

    public function scopeOneTime($query)
    {
        return $query->where('type', 'one-time');
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    // Accessors
    public function getFormattedPrice()
    {
        return 'Rs. ' . number_format($this->price, 2);
    }

    public function getDurationText()
    {
        if (!$this->duration_days) {
            return 'One-time';
        }

        if ($this->duration_days === 30) {
            return 'Monthly';
        } elseif ($this->duration_days === 365) {
            return 'Yearly';
        }

        return $this->duration_days . ' Days';
    }
}