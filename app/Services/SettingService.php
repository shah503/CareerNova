<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    /**
     * Get a setting value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Setting::get($key, $default);
    }

    /**
     * Set a setting value
     * 
     * @param string $key
     * @param mixed $value
     * @param string $status
     * @return Setting
     */
    public function set($key, $value, $status = 'active')
    {
        return Setting::set($key, $value, $status);
    }

    /**
     * Check if a feature is enabled
     * 
     * @param string $feature
     * @return bool
     */
    public function isFeatureEnabled($feature)
    {
        return Setting::isFeatureEnabled($feature);
    }

    /**
     * Enable a feature
     * 
     * @param string $feature
     * @return void
     */
    public function enableFeature($feature)
    {
        $this->set($feature, '1', 'active');
    }

    /**
     * Disable a feature
     * 
     * @param string $feature
     * @return void
     */
    public function disableFeature($feature)
    {
        $this->set($feature, '0', 'inactive');
    }

    /**
     * Check if system is active
     * 
     * @return bool
     */
    public function isSystemActive()
    {
        return Setting::isSystemActive();
    }

    /**
     * Get all payment settings
     * 
     * @return array
     */
    public function getPaymentSettings()
    {
        return [
            'jazzcash_number' => $this->get(Setting::JAZZCASH_NUMBER),
            'easypaisa_number' => $this->get(Setting::EASYPAISA_NUMBER),
            'bank_iban' => $this->get(Setting::BANK_IBAN),
            'bank_title' => $this->get(Setting::BANK_TITLE),
        ];
    }

    /**
     * Get all system settings
     * 
     * @return array
     */
    public function getAllSettings()
    {
        $settings = Setting::all();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = [
                'value' => $setting->value,
                'status' => $setting->status,
            ];
        }

        return $result;
    }

    /**
     * Update multiple settings
     * 
     * @param array $data
     * @return void
     */
    public function updateMultiple($data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
}