<?php

namespace App\Modules\Vendor\Vouchers\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Vendor\Vouchers\Services\VoucherCheckService;
use App\Modules\Vendor\Vouchers\Requests\CheckVoucherRequest;
use App\Modules\Vendor\Vouchers\Resources\VoucherCheckResource;
use App\Support\Exceptions\ApiException;
use Illuminate\Support\Facades\Auth;

class VouchersController extends BaseApiController
{
    protected VoucherCheckService $service;

    public function __construct(VoucherCheckService $service)
    {
        $this->service = $service;
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
            $voucher = $this->service->check($vendor, $data['code_or_link']);
            return $this->success(new VoucherCheckResource($voucher), 'vendor.vouchers.check_success');
        } catch (ApiException $e) {
            return $this->error($e->message, null, $e->statusCode);
        }
    }
}
