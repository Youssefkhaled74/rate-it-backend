<?php

namespace App\Modules\Vendor\Branches\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Vendor\Branches\Services\BranchService;
use App\Modules\Vendor\Branches\Requests\UpdateBranchCooldownRequest;
use App\Modules\Vendor\Branches\Resources\BranchDetailsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends BaseApiController
{
    protected BranchService $service;

    public function __construct(BranchService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/v1/vendor/branches
     * 
     * List branches accessible by vendor
     * - VENDOR_ADMIN: all branches in brand
     * - BRANCH_STAFF: only their branch
     */
    public function index(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        try {
            $branches = $this->service->listBranches($vendor);
            return $this->success(BranchDetailsResource::collection($branches), 'vendor.branches.list');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 403);
        }
    }

    /**
     * GET /api/v1/vendor/branches/{branchId}
     * 
     * View branch details
     */
    public function show(Request $request, $branchId)
    {
        $vendor = Auth::guard('vendor')->user();

        try {
            $branch = $this->service->getBranch($vendor, (int) $branchId);
            return $this->success(new BranchDetailsResource($branch), 'vendor.branch.details');
        } catch (\Exception $e) {
            $statusCode = $e instanceof \App\Support\Exceptions\ApiException ? $e->getCode() : 403;
            return $this->error($e->getMessage(), null, $statusCode ?: 403);
        }
    }

    /**
     * PATCH /api/v1/vendor/branches/{branchId}/cooldown
     * 
     * Update review cooldown days
     * VENDOR_ADMIN only
     */
    public function updateCooldown(UpdateBranchCooldownRequest $request, $branchId)
    {
        $vendor = Auth::guard('vendor')->user();
        $data = $request->validated();

        try {
            $branch = $this->service->updateCooldown(
                $vendor,
                (int) $branchId,
                $data['review_cooldown_days']
            );
            return $this->success(new BranchDetailsResource($branch), 'vendor.branch.cooldown.updated');
        } catch (\Exception $e) {
            $statusCode = $e instanceof \App\Support\Exceptions\ApiException ? $e->getCode() : 403;
            return $this->error($e->getMessage(), null, $statusCode ?: 403);
        }
    }
}
