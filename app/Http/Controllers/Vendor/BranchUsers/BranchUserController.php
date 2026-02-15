<?php

namespace App\Http\Controllers\Vendor\BranchUsers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\BranchStaffStoreRequest;
use App\Http\Requests\Vendor\BranchStaffUpdateRequest;
use App\Modules\Vendor\Staff\Services\VendorStaffService;
use App\Modules\Vendor\Branches\Services\BranchService;
use Illuminate\Support\Facades\Auth;

class BranchUserController extends Controller
{
    protected VendorStaffService $service;
    protected BranchService $branchService;

    public function __construct(VendorStaffService $service, BranchService $branchService)
    {
        $this->service = $service;
        $this->branchService = $branchService;
    }

    public function index()
    {
        $vendor = Auth::guard('vendor_web')->user();
        if (! $vendor) {
            abort(403);
        }
        $vendor->loadMissing('brand', 'branch');

        $filters = request()->only(['branch_id', 'q', 'is_active']);
        $staff = $this->service->list($vendor, $filters);
        $branches = $this->branchService->listBranches($vendor);

        return view('vendor.branch_users.index', [
            'vendor' => $vendor,
            'staff' => $staff,
            'branches' => $branches,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $vendor = Auth::guard('vendor_web')->user();
        if (! $vendor) {
            abort(403);
        }
        $vendor->loadMissing('brand', 'branch');
        $branches = $this->branchService->listBranches($vendor);

        return view('vendor.branch_users.create', [
            'vendor' => $vendor,
            'branches' => $branches,
        ]);
    }

    public function store(BranchStaffStoreRequest $request)
    {
        $vendor = Auth::guard('vendor_web')->user();
        if (! $vendor) {
            abort(403);
        }
        $vendor->loadMissing('brand', 'branch');

        $staff = $this->service->create($vendor, $request->validated());

        return redirect()
            ->route('vendor.staff.index')
            ->with('success', __('vendor.staff.created'))
            ->with('temporary_password', $staff->temporary_password ?? null);
    }

    public function edit(int $id)
    {
        $vendor = Auth::guard('vendor_web')->user();
        if (! $vendor) {
            abort(403);
        }
        $vendor->loadMissing('brand', 'branch');

        $staff = $this->service->find($vendor, $id);
        if (! $staff) {
            abort(404);
        }

        $branches = $this->branchService->listBranches($vendor);

        return view('vendor.branch_users.edit', [
            'vendor' => $vendor,
            'staff' => $staff,
            'branches' => $branches,
        ]);
    }

    public function update(int $id, BranchStaffUpdateRequest $request)
    {
        $vendor = Auth::guard('vendor_web')->user();
        if (! $vendor) {
            abort(403);
        }
        $vendor->loadMissing('brand', 'branch');

        $staff = $this->service->update($vendor, $id, $request->validated());
        if (! $staff) {
            abort(404);
        }

        return redirect()->route('vendor.staff.index')->with('success', __('vendor.staff.updated'));
    }

    public function destroy(int $id)
    {
        $vendor = Auth::guard('vendor_web')->user();
        if (! $vendor) {
            abort(403);
        }
        $vendor->loadMissing('brand', 'branch');

        $deleted = $this->service->delete($vendor, $id);
        if (! $deleted) {
            abort(404);
        }

        return redirect()->route('vendor.staff.index')->with('success', __('vendor.staff.deleted'));
    }
}

