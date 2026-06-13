<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

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

    public function leaderboard()
    {
        return $this->hasOne(Leaderboard::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function aiMcqLogs()
    {
        return $this->hasMany(AiMcqLog::class);
    }

    public function createdMcqs()
    {
        return $this->hasMany(Mcq::class, 'created_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    // ✅ For Teachers
    public function teachingStudents()
    {
        return $this->belongsToMany(User::class, 'student_teacher', 'teacher_id', 'student_id')
            ->wherePivot('deleted_at', null);
    }

    /**
     * For Teachers: Get all exam sessions from MCQs they created
     */
    public function studentExamSessions()
    {
        return ExamSession::whereIn(
            'subject_id',
            Mcq::where('created_by', $this->id)->pluck('subject_id')
        );
    }

    /**
     * For Parents: Get students in same batch (basic synchronization)
     */
    public function studentsByBatch()
    {
        if ($this->role !== 'parent') {
            return collect([]);
        }
    
        return User::where('role', 'student')
            ->where('batch', $this->batch);
    }

    // ✅ For Students to see their teachers
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'student_teacher', 'student_id', 'teacher_id')
            ->wherePivot('deleted_at', null);
    }

    // ✅ For Parents
    public function studentChildren()
    {
        return $this->belongsToMany(User::class, 'student_parent', 'parent_id', 'student_id')
            ->wherePivot('deleted_at', null);
    }

    // ✅ For Students to see their parents
    public function parents()
    {
        return $this->belongsToMany(User::class, 'student_parent', 'student_id', 'parent_id')
            ->wherePivot('deleted_at', null);
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

    public function scopeParent($query)
    {
        return $query->where('role', 'parent');
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