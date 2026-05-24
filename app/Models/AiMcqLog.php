<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiMcqLog extends Model
{
    use HasFactory;

    protected $table = 'ai_mcq_logs';

    protected $fillable = [
        'user_id',
        'chapter',
        'topic',
        'sub_topic',
        'quantity_requested',
        'quantity_generated',
        'prompt',
        'response_json',
        'status',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeGenerating($query)
    {
        return $query->where('status', 'generating');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function getGeneratedMcqs()
    {
        if (!$this->response_json) {
            return [];
        }

        return json_decode($this->response_json, true) ?? [];
    }

    public function getStatusBadge()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-secondary">Pending</span>',
            'generating' => '<span class="badge bg-info">Generating...</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'failed' => '<span class="badge bg-danger">Failed</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}