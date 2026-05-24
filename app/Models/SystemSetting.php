<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'module_name', 'enabled', 'configuration', 'description', 'updated_by'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'configuration' => 'array',
    ];

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function isModuleEnabled($moduleName)
    {
        $setting = self::where('module_name', $moduleName)->first();
        return $setting ? $setting->enabled : true;
    }
}