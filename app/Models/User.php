<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'batch',
        'phone',
        'profile_photo',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function answerLogs()
    {
        return $this->hasMany(AnswerLog::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function leaderboard()
    {
        return $this->hasOne(Leaderboard::class);
    }

    public function aiMcqLogs()
    {
        return $this->hasMany(AiMcqLog::class);
    }

    public function createdMcqs()
    {
        return $this->hasMany(Mcq::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeStudent($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeTeacher($query)
    {
        return $query->where('role', 'teacher');
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    // Accessors
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function isParent()
    {
        return $this->role === 'parent';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}