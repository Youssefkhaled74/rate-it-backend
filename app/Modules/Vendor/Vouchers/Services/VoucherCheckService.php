<?php

namespace App\Modules\Vendor\Vouchers\Services;

use App\Models\Voucher;
use App\Models\VendorUser;
use App\Support\Traits\Vendor\VendorScoping;
use App\Support\Exceptions\ApiException;

class VoucherCheckService
{
    use VendorScoping;

    /**
     * Check voucher status
     * Accepts code or link, normalizes to code
     * Returns voucher with status and details
     */
    public function check(VendorUser $vendor, string $codeOrLink): ?Voucher
    {
        // Normalize input to extract voucher code
        $code = $this->normalizeCode($codeOrLink);

        if (! $code) {
            throw new ApiException(__('vendor.vouchers.invalid_code'), 422);
        }

        // Query voucher
        $voucher = Voucher::where('code', $code)
            ->with(['brand', 'usedBranch', 'verifiedByVendor'])
            ->first();

        if (! $voucher) {
            throw new ApiException(__('vendor.vouchers.not_found'), 404);
        }

        // Enforce brand scoping
        $brandId = $this->getVendorBrandId($vendor);
        if ($voucher->brand_id !== $brandId) {
            throw new ApiException(__('auth.forbidden'), 403);
        }

        return $voucher;
    }

    /**
     * Normalize code from various formats
     * Supports: "CODE123", "VOUCHER-ABC123", links with query params, etc.
     * Returns normalized uppercase code
     */
    protected function normalizeCode(string $input): ?string
    {
        // Trim whitespace
        $input = trim($input);

        if (empty($input)) {
            return null;
        }

        // If it's a URL/link, try to extract code from query parameters
        if (filter_var($input, FILTER_VALIDATE_URL)) {
            return $this->extractCodeFromUrl($input);
        }

        // If it contains hyphens (VOUCHER-ABC123), extract the part after hyphen
        if (strpos($input, '-') !== false) {
            $parts = explode('-', $input);
            $code = end($parts); // Get last part
            return strtoupper(trim($code)) ?: null;
        }

        // Otherwise, use input as-is (uppercase)
        return strtoupper($input) ?: null;
    }

    /**
     * Extract voucher code from URL
     * Looks for 'code' or 'voucher' query parameters
     */
    protected function extractCodeFromUrl(string $url): ?string
    {
        $parsed = parse_url($url);
        
        if (! isset($parsed['query'])) {
            return null;
        }

        parse_str($parsed['query'], $queryParams);

        // Look for 'code' or 'voucher' parameter
        $code = $queryParams['code'] ?? $queryParams['voucher'] ?? null;

        return $code ? strtoupper(trim($code)) : null;
    }
}
