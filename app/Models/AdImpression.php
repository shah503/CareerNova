<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdImpression extends Model
{
    protected $fillable = [
        'ad_campaign_id', 'user_id', 'slot_name', 'user_ip', 'user_agent', 'clicked', 'clicked_at'
    ];

    protected $casts = [
        'clicked' => 'boolean',
        'clicked_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'ad_campaign_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}