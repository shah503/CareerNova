<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'status',
    ];

    // Common settings keys
    const SITE_NAME = 'site_name';
    const SUPPORT_EMAIL = 'support_email';
    const WHATSAPP_SUPPORT = 'whatsapp_support';
    const JAZZCASH_NUMBER = 'jazzcash_number';
    const EASYPAISA_NUMBER = 'easypaisa_number';
    const BANK_IBAN = 'bank_iban';
    const BANK_TITLE = 'bank_title';

    // Feature toggles
    const FEATURE_AI_MCQ = 'feature_ai_mcq';
    const FEATURE_CSV_IMPORT = 'feature_csv_import';
    const FEATURE_PAYMENTS = 'feature_payments';
    const FEATURE_ANALYTICS = 'feature_analytics';
    const SYSTEM_ACTIVE = 'system_active';

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    // Static methods for easy access
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting?->value ?? $default;
    }

    public static function set($key, $value, $status = 'active')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'status' => $status]
        );
    }

    public static function toggle($key, $status = 'active')
    {
        $currentStatus = self::get($key . '_status', 'inactive');
        $newStatus = $currentStatus === 'active' ? 'inactive' : 'active';

        return self::set($key, null, $newStatus);
    }

    public static function isFeatureEnabled($feature)
    {
        return self::where('key', $feature)
            ->where('status', 'active')
            ->exists();
    }

    public static function isSystemActive()
    {
        return self::isFeatureEnabled(self::SYSTEM_ACTIVE);
    }

    // Methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getValueFormatted()
    {
        return is_numeric($this->value) ? number_format($this->value, 2) : $this->value;
    }
}