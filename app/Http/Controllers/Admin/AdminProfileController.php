<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminProfileService;
use App\Http\Requests\Admin\UpdateAdminPhotoRequest;

class AdminProfileController extends Controller
{
    protected AdminProfileService $service;

    public function __construct(AdminProfileService $service)
    {
        $this->service = $service;
    }

    public function edit(Request $request)
    {
        $admin = auth()->guard('admin_web')->user();
        return view('admin.profile.edit', compact('admin'));
    }

    public function updatePhoto(UpdateAdminPhotoRequest $request)
    {
        $admin = auth()->guard('admin_web')->user();
        // controller-level authorization (only self) — super admins allowed too
        if (! $admin) return redirect()->route('admin.login');

        $file = $request->file('photo');
        $this->service->updatePhoto($admin, $file);

        return back()->with('success', 'Profile photo updated');
    }

    public function removePhoto(Request $request)
    {
        $admin = auth()->guard('admin_web')->user();
        if (! $admin) return redirect()->route('admin.login');
        $this->service->removePhoto($admin);
        return back()->with('success','Photo removed');
    }
}
