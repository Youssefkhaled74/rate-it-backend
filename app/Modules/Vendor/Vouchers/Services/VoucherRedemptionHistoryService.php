<?php

namespace App\Modules\Vendor\Vouchers\Services;

use App\Models\Voucher;
use App\Models\VendorUser;
use App\Models\Branch;
use App\Support\Traits\Vendor\VendorScoping;
use App\Support\Traits\Vendor\VendorRoleCheck;
use Illuminate\Pagination\LengthAwarePaginator;

class VoucherRedemptionHistoryService
{
    use VendorScoping;
    use VendorRoleCheck;

    /**
     * List voucher redemptions (USED/EXPIRED vouchers)
     * 
     * For VENDOR_ADMIN: shows all brand's vouchers, can filter by branch
     * For BRANCH_STAFF: shows only their branch's vouchers
     */
    public function list(VendorUser $vendor, array $filters): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 15);
        $page = (int) ($filters['page'] ?? 1);
        
        // Get vendor's brand ID
        $brandId = $this->getVendorBrandId($vendor);
        
        // Start query: only vouchers from vendor's brand
        $query = Voucher::query()
            ->with(['brand:id,name,name_en,name_ar', 'usedBranch:id,name,name_en,name_ar', 'verifiedByVendor:id,name'])
            ->where('brand_id', $brandId);

        // Handle branch filtering based on role
        if ($this->isBranchStaff($vendor)) {
            // BRANCH_STAFF: Force to their branch only
            $query->where('used_branch_id', $vendor->branch_id);
        } elseif (!empty($filters['branch_id'])) {
            // VENDOR_ADMIN: Verify branch belongs to their brand
            $branchId = (int) $filters['branch_id'];
            
            // Verify branch belongs to vendor's brand
            $branch = Branch::with('place')->find($branchId);
            if ($branch && $branch->place->brand_id === $brandId) {
                $query->where('used_branch_id', $branchId);
            } else {
                // Invalid branch, return empty paginator
                return Voucher::paginate(0);
            }
        }

        // Filter by status (only USED or EXPIRED for redemption history)
        if (!empty($filters['status'])) {
            $status = $filters['status'];
            if (in_array($status, ['USED', 'EXPIRED'])) {
                $query->where('status', $status);
            }
        } else {
            // Default: show only redeemed vouchers (USED + EXPIRED)
            $query->whereIn('status', ['USED', 'EXPIRED']);
        }

        // Filter by date range (used_at)
        if (!empty($filters['date_from'])) {
            $query->whereDate('used_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('used_at', '<=', $filters['date_to']);
        }

        // Order by most recent redemption first
        $query->orderBy('used_at', 'desc');

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
