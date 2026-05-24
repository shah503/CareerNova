<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSession extends Model
{
    protected $fillable = [

        'session_token',
        'mcq_ids',
        'time_minutes',
        'submitted'

    ];
}