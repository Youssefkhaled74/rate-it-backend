<?php

namespace App\Modules\Vendor\Vouchers\Services;

use App\Models\Voucher;
use App\Models\VendorUser;
use App\Models\Branch;
use App\Support\Traits\Vendor\VendorScoping;
use App\Support\Traits\Vendor\VendorRoleCheck;
use App\Support\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VoucherRedeemService
{
    use VendorScoping;
    use VendorRoleCheck;

    /**
     * Redeem voucher atomically with row locking
     * 
     * Rules:
     * - BRANCH_STAFF: branch_id is forced to their assigned branch
     * - VENDOR_ADMIN: branch_id must be provided and belong to brand
     * - Transaction ensures atomicity
     * - Row lock (SELECT ... FOR UPDATE) prevents race conditions
     */
    public function redeem(VendorUser $vendor, string $codeOrLink, ?int $branchId): Voucher
    {
        // Handle branch_id based on role
        if ($this->isBranchStaff($vendor)) {
            // BRANCH_STAFF: Force to their branch
            $branchId = $vendor->branch_id;
        } else {
            // VENDOR_ADMIN: Must provide branch_id
            if (! $branchId) {
                throw new ApiException(__('vendor.vouchers.branch_id_required'), 422);
            }
        }

        // Verify branch belongs to vendor's brand
        $brandId = $this->getVendorBrandId($vendor);
        $branch = Branch::find($branchId);
        if (! $branch || (int) $branch->brand_id !== (int) $brandId) {
            throw new ApiException(__('auth.forbidden'), 403);
        }

        // Normalize code
        $code = $this->normalizeCode($codeOrLink);
        if (! $code) {
            throw new ApiException(__('vendor.vouchers.invalid_code'), 422);
        }

        // Atomic transaction with row lock
        return DB::transaction(function () use ($code, $branchId, $brandId, $vendor) {
            // SELECT FOR UPDATE ensures only one process can update this voucher at a time
            $voucher = Voucher::where('code', $code)
                ->lockForUpdate()
                ->first();

            // Voucher not found
            if (! $voucher) {
                throw new ApiException(__('vendor.vouchers.not_found'), 404);
            }

            // Voucher belongs to different brand
            if ($voucher->brand_id !== $brandId) {
                throw new ApiException(__('auth.forbidden'), 403);
            }

            // Check voucher status
            if ($voucher->status === 'USED') {
                throw new ApiException(__('vendor.vouchers.already_redeemed'), 422);
            }

            // Check if expired
            if ($voucher->status === 'EXPIRED') {
                throw new ApiException(__('vendor.vouchers.already_expired'), 422);
            }

            // Check expiry date (if expires_at is set and in past)
            if ($voucher->expires_at && $voucher->expires_at->isPast()) {
                // Mark as expired if not already
                if ($voucher->status !== 'EXPIRED') {
                    $voucher->status = 'EXPIRED';
                    $voucher->save();
                }
                throw new ApiException(__('vendor.vouchers.voucher_expired'), 422);
            }

            // Redeem the voucher
            $voucher->status = 'USED';
            $voucher->used_at = Carbon::now();
            $voucher->used_branch_id = $branchId;
            $voucher->verified_by_vendor_user_id = $vendor->id;
            $voucher->save();

            // Reload with relationships
            return $voucher->load(['brand', 'usedBranch', 'verifiedByVendor']);
        });
    }

    /**
     * Normalize code from various formats
     * Same logic as check endpoint
     */
    protected function normalizeCode(string $input): ?string
    {
        $input = trim($input);

        if (empty($input)) {
            return null;
        }

        if (filter_var($input, FILTER_VALIDATE_URL)) {
            return $this->extractCodeFromUrl($input);
        }

        if (strpos($input, '-') !== false) {
            $parts = explode('-', $input);
            $code = end($parts);
            return strtoupper(trim($code)) ?: null;
        }

        return strtoupper($input) ?: null;
    }

    /**
     * Extract voucher code from URL
     */
    protected function extractCodeFromUrl(string $url): ?string
    {
        $parsed = parse_url($url);

        if (! isset($parsed['query'])) {
            return null;
        }

        parse_str($parsed['query'], $queryParams);

        $code = $queryParams['code'] ?? $queryParams['voucher'] ?? null;

        return $code ? strtoupper(trim($code)) : null;
    }
}
