<?php

namespace App\Modules\Vendor\Staff\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Vendor\Staff\Services\VendorStaffService;
use App\Modules\Vendor\Staff\Requests\ListStaffRequest;
use App\Modules\Vendor\Staff\Requests\StoreStaffRequest;
use App\Modules\Vendor\Staff\Requests\UpdateStaffRequest;
use App\Modules\Vendor\Staff\Requests\ResetStaffPasswordRequest;
use App\Modules\Vendor\Staff\Resources\StaffListResource;
use App\Modules\Vendor\Staff\Resources\StaffDetailResource;
use Illuminate\Support\Facades\Auth;

class StaffController extends BaseApiController
{
    protected VendorStaffService $service;

    public function __construct(VendorStaffService $service)
    {
        $this->service = $service;
    }

    /**
     * List staff members
     * VENDOR_ADMIN only
     */
    public function index(ListStaffRequest $request)
    {
        $vendor = Auth::guard('vendor')->user();
        $filters = $request->validated();
        
        $paginator = $this->service->list($vendor, $filters);
        return $this->paginated($paginator, 'vendor.staff.list', StaffListResource::class);
    }

    /**
     * Get staff member details
     * VENDOR_ADMIN only
     */
    public function show(string $id)
    {
        $vendor = Auth::guard('vendor')->user();
        $staff = $this->service->find($vendor, (int) $id);
        
        if (! $staff) {
            return $this->error(__('vendor.staff.not_found'), null, 404);
        }

        return $this->success(new StaffDetailResource($staff), 'vendor.staff.details');
    }

    /**
     * Create new staff member
     * VENDOR_ADMIN only
     */
    public function store(StoreStaffRequest $request)
    {
        $vendor = Auth::guard('vendor')->user();
        $data = $request->validated();
        
        try {
            $staff = $this->service->create($vendor, $data);
            
            return $this->success(
                new StaffDetailResource($staff),
                'vendor.staff.created',
                201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 422);
        }
    }

    /**
     * Update staff member
     * VENDOR_ADMIN only
     */
    public function update(string $id, UpdateStaffRequest $request)
    {
        $vendor = Auth::guard('vendor')->user();
        $data = $request->validated();
        
        try {
            $staff = $this->service->update($vendor, (int) $id, $data);
            
            if (! $staff) {
                return $this->error(__('vendor.staff.not_found'), null, 404);
            }

            return $this->success(new StaffDetailResource($staff), 'vendor.staff.updated');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 422);
        }
    }

    /**
     * Reset staff password
     * VENDOR_ADMIN only
     */
    public function resetPassword(string $id, ResetStaffPasswordRequest $request)
    {
        $vendor = Auth::guard('vendor')->user();
        $data = $request->validated();
        
        try {
            $staff = $this->service->resetPassword($vendor, (int) $id, $data['new_password']);
            
            if (! $staff) {
                return $this->error(__('vendor.staff.not_found'), null, 404);
            }

            return $this->success(new StaffDetailResource($staff), 'vendor.staff.password_reset');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 422);
        }
    }
}
