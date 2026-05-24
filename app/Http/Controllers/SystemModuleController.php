<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Show module control center
     */
    public function index()
    {
        // Initialize default modules
        $defaultModules = [
            'chatbot' => 'Shahjee AI Chatbot',
            'ads' => 'Display Advertisements',
            'reporting' => 'Student MCQ Reporting',
            'ai_generation' => 'AI MCQ Generation',
            'leaderboards' => 'Leaderboard System',
            'registration' => 'User Registration',
            'timed_tests' => 'Timed Exam Sessions',
            'study_groups' => 'Study Groups',
            'analytics' => 'Performance Analytics',
            'notifications' => 'Email Notifications',
        ];

        foreach ($defaultModules as $name => $description) {
            SystemSetting::firstOrCreate(
                ['module_name' => $name],
                ['description' => $description, 'enabled' => true]
            );
        }

        $modules = SystemSetting::all();

        return view('admin.system-modules.index', compact('modules'));
    }

    /**
     * Toggle module status
     */
    public function toggle($moduleId)
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
            'configuration' => $request->configuration ?? [],
            'updated_by' => auth()->id(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Module configuration updated');
    }
}