<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];

    // Relationships
    public function mcqs()
    {
        return $this->hasMany(Mcq::class);
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    // Accessors
    public function getActiveMcqCount()
    {
        return $this->mcqs()->where('status', 'active')->count();
    }

    public function getTotalMcqCount()
    {
        return $this->mcqs()->count();
    }
}