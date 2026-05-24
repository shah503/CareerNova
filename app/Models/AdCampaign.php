<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    protected $fillable = [
        'campaign_name', 'advertiser_name', 'advertiser_email', 'advertiser_phone',
        'campaign_description', 'ad_slots', 'ad_type', 'ad_url', 'ad_image', 'ad_html',
        'cpc_rate', 'cpm_rate', 'start_date', 'end_date', 'status', 'total_amount', 'amount_paid'
    ];

    protected $casts = [
        'ad_slots' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function impressions()
    {
        return $this->hasMany(AdImpression::class);
    }

    public function getClickThroughRateAttribute()
    {
        if($this->impressions()->count() === 0) return 0;
        return round(($this->clicks / $this->impressions()->count()) * 100, 2);
    }

    public function getEarningsAttribute()
    {
        $cpm_earnings = ($this->impressions()->count() / 1000) * $this->cpm_rate;
        $cpc_earnings = $this->clicks * $this->cpc_rate;
        return $cpm_earnings + $cpc_earnings;
    }
}