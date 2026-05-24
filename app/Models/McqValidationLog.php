<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class McqValidationLog extends Model
{
    protected $table = 'mcq_validation_logs';

    protected $fillable = [
        'mcq_id', 'validation_type', 'passed', 'issues', 'details'
    ];

    protected $casts = [
        'passed' => 'boolean',
        'details' => 'array',
    ];

    public function mcq()
    {
        return $this->belongsTo(Mcq::class);
    }
}