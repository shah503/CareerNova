<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    
    }

    /**
     * Show settings page
     */
    public function index()
    {
        $settings = $this->settingService->getAllSettings();
        $paymentSettings = $this->settingService->getPaymentSettings();
        
        $features = [
            'system_active' => $this->settingService->isSystemActive(),
            'ai_mcq' => $this->settingService->isFeatureEnabled('feature_ai_mcq'),
            'csv_import' => $this->settingService->isFeatureEnabled('feature_csv_import'),
            'payments' => $this->settingService->isFeatureEnabled('feature_payments'),
            'analytics' => $this->settingService->isFeatureEnabled('feature_analytics'),
        ];

        return view('admin.settings.index', compact('settings', 'paymentSettings', 'features'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string',
            'support_email' => 'required|email',
            'whatsapp_support' => 'nullable|string',
        ]);

        $this->settingService->updateMultiple($validated);

        return back()->with('success', 'General settings updated!');
    }

    /**
     * Update payment settings
     */
    public function updatePayment(Request $request)
    {
        $validated = $request->validate([
            'jazzcash_number' => 'nullable|string',
            'easypaisa_number' => 'nullable|string',
            'bank_iban' => 'nullable|string',
            'bank_title' => 'nullable|string',
        ]);

        $this->settingService->updateMultiple($validated);

        return back()->with('success', 'Payment settings updated!');
    }

    /**
     * Toggle feature
     */
    public function toggleFeature(Request $request)
    {
        $feature = $request->input('feature');

        if ($this->settingService->isFeatureEnabled($feature)) {
            $this->settingService->disableFeature($feature);
            $message = 'Feature disabled!';
        } else {
            $this->settingService->enableFeature($feature);
            $message = 'Feature enabled!';
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }

    /**
     * Toggle system status
     */
    public function toggleSystem(Request $request)
    {
        if ($this->settingService->isSystemActive()) {
            $this->settingService->disableFeature('system_active');
            $message = 'System deactivated!';
        } else {
            $this->settingService->enableFeature('system_active');
            $message = 'System activated!';
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }
}