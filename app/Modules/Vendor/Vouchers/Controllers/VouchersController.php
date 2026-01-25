<?php

namespace App\Modules\Vendor\Vouchers\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Vendor\Vouchers\Services\VoucherCheckService;
use App\Modules\Vendor\Vouchers\Services\VoucherRedeemService;
use App\Modules\Vendor\Vouchers\Requests\CheckVoucherRequest;
use App\Modules\Vendor\Vouchers\Requests\RedeemVoucherRequest;
use App\Modules\Vendor\Vouchers\Resources\VoucherCheckResource;
use App\Modules\Vendor\Vouchers\Resources\VoucherRedeemResource;
use App\Support\Exceptions\ApiException;
use Illuminate\Support\Facades\Auth;

class VouchersController extends BaseApiController
{
    protected VoucherCheckService $checkService;
    protected VoucherRedeemService $redeemService;

    public function __construct(VoucherCheckService $checkService, VoucherRedeemService $redeemService)
    {
        $this->checkService = $checkService;
        $this->redeemService = $redeemService;
    }

    /**
     * Check voucher status
     * Both VENDOR_ADMIN and BRANCH_STAFF can access
     */
    public function check(CheckVoucherRequest $request)
    {
        $vendor = Auth::guard('vendor')->user();
        $data = $request->validated();

        try {
            $voucher = $this->checkService->check($vendor, $data['code_or_link']);
            return $this->success(new VoucherCheckResource($voucher), 'vendor.vouchers.check_success');
        } catch (ApiException $e) {
            return $this->error($e->getMessage(), null, $e->getStatusCode());
        }
    }

    /**
     * Redeem voucher
     * Both VENDOR_ADMIN and BRANCH_STAFF can access
     * 
     * BRANCH_STAFF: branch_id is forced to their assigned branch
     * VENDOR_ADMIN: branch_id must be provided
     */
    public function redeem(RedeemVoucherRequest $request)
    {
        $vendor = Auth::guard('vendor')->user();
        $data = $request->validated();

        try {
            $voucher = $this->redeemService->redeem(
                $vendor,
                $data['code_or_link'],
                $data['branch_id'] ?? null
            );
            return $this->success(new VoucherRedeemResource($voucher), 'vendor.vouchers.redeemed');
        } catch (ApiException $e) {
            return $this->error($e->getMessage(), null, $e->getStatusCode());
        }
    }
}
