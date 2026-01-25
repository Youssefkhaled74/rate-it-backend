<?php

namespace App\Modules\Admin\Vendors\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Admin\Vendors\Services\VendorAdminService;
use App\Modules\Admin\Vendors\Requests\ListVendorsRequest;
use App\Modules\Admin\Vendors\Requests\CreateVendorRequest;
use App\Modules\Admin\Vendors\Resources\VendorResource;
use App\Models\VendorUser;

class VendorsController extends BaseApiController
{
    protected VendorAdminService $service;

    public function __construct(VendorAdminService $service)
    {
        $this->service = $service;
    }

    /**
     * List all vendor admin accounts
     * GET /api/v1/admin/vendors
     */
    public function index(ListVendorsRequest $request)
    {
        $filters = $request->validated();
        $vendors = $this->service->list($filters);
        return $this->paginated($vendors, 'admin.vendors.list');
    }

    /**
     * Get single vendor details
     * GET /api/v1/admin/vendors/{id}
     */
    public function show(string $id)
    {
        $vendor = $this->service->find((int)$id);
        if (!$vendor) {
            return $this->error(__('admin.vendors.not_found'), null, 404);
        }
        return $this->success(new VendorResource($vendor), 'admin.vendors.details');
    }

    /**
     * Create new vendor admin account
     * POST /api/v1/admin/vendors
     */
    public function store(CreateVendorRequest $request)
    {
        $data = $request->validated();
        $vendor = $this->service->create($data);
        $vendor->load('brand');
        return $this->success(new VendorResource($vendor), 'admin.vendors.created', 201);
    }

    /**
     * Update vendor details
     * PATCH /api/v1/admin/vendors/{id}
     */
    public function update(string $id, CreateVendorRequest $request)
    {
        $vendor = $this->service->find((int)$id);
        if (!$vendor) {
            return $this->error(__('admin.vendors.not_found'), null, 404);
        }

        $data = $request->validated();
        $vendor = $this->service->update($vendor, $data);
        $vendor->load('brand');
        return $this->success(new VendorResource($vendor), 'admin.vendors.updated');
    }

    /**
     * Delete vendor account
     * DELETE /api/v1/admin/vendors/{id}
     */
    public function destroy(string $id)
    {
        $vendor = $this->service->find((int)$id);
        if (!$vendor) {
            return $this->error(__('admin.vendors.not_found'), null, 404);
        }

        $this->service->delete($vendor);
        return $this->success(null, 'admin.vendors.deleted');
    }

    /**
     * Restore deleted vendor
     * POST /api/v1/admin/vendors/{id}/restore
     */
    public function restore(string $id)
    {
        $vendor = VendorUser::withTrashed()
            ->where('role', 'VENDOR_ADMIN')
            ->find((int)$id);

        if (!$vendor || !$vendor->trashed()) {
            return $this->error(__('admin.vendors.not_found'), null, 404);
        }

        $this->service->restore($vendor);
        $vendor->load('brand');
        return $this->success(new VendorResource($vendor), 'admin.vendors.restored');
    }
}
