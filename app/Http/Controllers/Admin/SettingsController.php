<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionSetting;

class SettingsController extends Controller
{
    public function index()
    {
        $freeTrialDays = SubscriptionSetting::getFreeTrialDays();
        return view('admin.settings.index', compact('freeTrialDays'));
    }

    public function updateSubscription(Request $request)
    {
        $data = $request->validate([
            'free_trial_days' => ['required', 'integer', 'min:0', 'max:3650'],
        ]);

        $adminId = auth()->guard('admin_web')->id();
        SubscriptionSetting::updateOrCreate(
            ['id' => 1],
            [
                'free_trial_days' => (int) $data['free_trial_days'],
                'is_active' => true,
                'created_by_admin_id' => $adminId,
                'activated_at' => now(),
            ]
        );

        return redirect()
            ->back()
            ->with('success', __('admin.settings_saved'));
    }
}
