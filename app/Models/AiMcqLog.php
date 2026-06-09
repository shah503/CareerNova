<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiMcqLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ai_mcq_logs';

    protected $fillable = [
        'user_id',
        'topic',
        'generated_mcqs',
        'response_json',
    ];

    protected $casts = [
        'response_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}