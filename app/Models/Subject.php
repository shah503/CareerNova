<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'description',
    ];

    public function mcqs()
    {
        return $this->hasMany(Mcq::class);
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}