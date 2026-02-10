<?php

namespace App\Http\Controllers\Vendor\Branches;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\BranchCooldownRequest;
use App\Modules\Vendor\Branches\Services\BranchService;
use App\Support\Exceptions\ApiException;
use Illuminate\Support\Facades\Auth;

class BranchSettingsController extends Controller
{
    protected BranchService $service;

    public function __construct(BranchService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $vendor = Auth::guard('vendor_web')->user();
        $vendor?->loadMissing('brand', 'branch.place');

        $branches = $this->service->listBranches($vendor);

        return view('vendor.branches.settings', [
            'vendor' => $vendor,
            'branches' => $branches,
        ]);
    }

    public function updateCooldown(int $id, BranchCooldownRequest $request)
    {
        $vendor = Auth::guard('vendor_web')->user();
        $vendor?->loadMissing('brand', 'branch.place');

        $data = $request->validated();

        try {
            $this->service->updateCooldown($vendor, $id, (int) $data['review_cooldown_days']);
        } catch (ApiException $e) {
            return back()->withErrors(['review_cooldown_days' => $e->getMessage()]);
        }

        return back()->with('success', __('vendor.branches.cooldown_updated'));
    }
}

