<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Show module control panel
     */
    public function index()
    {
        $modules = SystemSetting::all();

        $defaultModules = [
            'chatbot' => 'Shahjee AI Chatbot',
            'ads' => 'Display Advertisements',
            'reporting' => 'MCQ Reporting System',
            'ai_generation' => 'AI MCQ Generation',
            'leaderboards' => 'Student Leaderboards',
            'registration' => 'User Registration',
            'timed_tests' => 'Timed Test Sessions',
            'study_groups' => 'Study Groups',
            'analytics' => 'Performance Analytics',
            'notifications' => 'Email Notifications',
        ];

        // Create missing settings
        foreach ($defaultModules as $name => $description) {
            SystemSetting::firstOrCreate(
                ['module_name' => $name],
                ['description' => $description, 'enabled' => true]
            );
        }

        $modules = SystemSetting::all();

        return view('admin.system-settings.index', compact('modules'));
    }

    /**
     * Toggle module
     */
    public function toggle(Request $request, $moduleId)
    {
        $setting = SystemSetting::findOrFail($moduleId);

        $setting->update([
            'enabled' => !$setting->enabled,
            'updated_by' => auth()->id(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'enabled' => $setting->enabled
        ]);
    }

    /**
     * Update module configuration
     */
    public function updateConfig(Request $request, $moduleId)
    {
        $setting = SystemSetting::findOrFail($moduleId);

        $setting->update([
            'configuration' => $request->all(),
            'updated_by' => auth()->id(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Configuration updated');
    }
}