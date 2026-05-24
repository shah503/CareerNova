<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = [
        'teacher_id', 'name', 'batch', 'description', 'students'
    ];

    protected $casts = [
        'students' => 'array',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function getStudentCountAttribute()
    {
        return is_array($this->students) ? count($this->students) : 0;
    }
}