<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_name',
        'enabled',
        'description',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public static function isModuleEnabled($moduleName)
    {
        $setting = self::where('module_name', $moduleName)->first();
        return $setting ? $setting->enabled : true;
    }
}