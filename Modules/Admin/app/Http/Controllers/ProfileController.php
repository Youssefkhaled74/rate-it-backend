<?php

namespace Modules\Admin\app\Http\Controllers;

use Modules\Admin\app\Http\Requests\ProfileUpdateRequest;
use Modules\Admin\app\Http\Requests\PasswordUpdateRequest;
use Modules\Admin\app\Services\ProfileService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function __construct(protected ProfileService $profileService)
    {
    }

    /**
     * Show profile.
     */
    public function show(): View
    {
        $admin = auth('admin')->user();
        $profileData = $this->profileService->getProfileData($admin);

        return view('admin::profile.show', ['admin' => $profileData]);
    }

    /**
     * Show edit profile form.
     */
    public function edit(): View
    {
        $admin = auth('admin')->user();

        return view('admin::profile.edit', ['admin' => $admin]);
    }

    /**
     * Update profile.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $admin = auth('admin')->user();
        $this->profileService->updateProfile($admin, $request->validated());

        return redirect()->route('admin.profile.show')
            ->with('success', __('admin.profile_updated'));
    }

    /**
     * Show change password form.
     */
    public function showChangePasswordForm(): View
    {
        return view('admin::profile.password');
    }

    /**
     * Update password.
     */
    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $admin = auth('admin')->user();
        $validated = $request->validated();

        try {
            $this->profileService->updatePassword(
                $admin,
                $validated['current_password'],
                $validated['password']
            );

            return redirect()->route('admin.profile.show')
                ->with('success', __('admin.password_updated'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
