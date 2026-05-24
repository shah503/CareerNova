<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class McqReport extends Model
{
    protected $table = 'mcq_reports';

    protected $fillable = [
        'user_id', 'mcq_id', 'issue_type', 'description',
        'suggested_correction', 'status', 'reviewed_by', 'admin_notes', 'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mcq()
    {
        return $this->belongsTo(Mcq::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}