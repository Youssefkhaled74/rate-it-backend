<?php

namespace App\Http\Controllers\Vendor\Vouchers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\VoucherCheckRequest;
use App\Http\Requests\Vendor\VoucherRedeemRequest;
use App\Http\Requests\Vendor\VoucherHistoryRequest;
use App\Modules\Vendor\Vouchers\Services\VoucherCheckService;
use App\Modules\Vendor\Vouchers\Services\VoucherRedeemService;
use App\Modules\Vendor\Vouchers\Services\VoucherRedemptionHistoryService;
use App\Modules\Vendor\Branches\Services\BranchService;
use App\Support\Exceptions\ApiException;
use Illuminate\Support\Facades\Auth;

class VoucherVerificationController extends Controller
{
    protected VoucherCheckService $checkService;
    protected VoucherRedeemService $redeemService;
    protected VoucherRedemptionHistoryService $historyService;
    protected BranchService $branchService;

    public function __construct(
        VoucherCheckService $checkService,
        VoucherRedeemService $redeemService,
        VoucherRedemptionHistoryService $historyService,
        BranchService $branchService
    ) {
        $this->checkService = $checkService;
        $this->redeemService = $redeemService;
        $this->historyService = $historyService;
        $this->branchService = $branchService;
    }

    public function verifyForm()
    {
        $vendor = Auth::guard('vendor_web')->user();
        $vendor?->loadMissing('brand', 'branch');

        $branches = $vendor->role === 'VENDOR_ADMIN' ? $this->branchService->listBranches($vendor) : collect();

        return view('vendor.vouchers.verify', [
            'vendor' => $vendor,
            'branches' => $branches,
            'voucher' => null,
            'status' => null,
        ]);
    }

    public function check(VoucherCheckRequest $request)
    {
        $vendor = Auth::guard('vendor_web')->user();
        $vendor?->loadMissing('brand', 'branch');

        try {
            $voucher = $this->checkService->check($vendor, $request->validated()['code_or_link']);
        } catch (ApiException $e) {
            return back()->withErrors(['code_or_link' => $e->getMessage()])->withInput();
        }

        $status = $this->resolveStatus($voucher);

        $branches = $vendor->role === 'VENDOR_ADMIN' ? $this->branchService->listBranches($vendor) : collect();

        return view('vendor.vouchers.verify', [
            'vendor' => $vendor,
            'branches' => $branches,
            'voucher' => $voucher,
            'status' => $status,
        ]);
    }

    public function redeem(VoucherRedeemRequest $request)
    {
        $vendor = Auth::guard('vendor_web')->user();
        $vendor?->loadMissing('brand', 'branch');

        $data = $request->validated();

        try {
            $voucher = $this->redeemService->redeem($vendor, $data['code_or_link'], $data['branch_id'] ?? null);
        } catch (ApiException $e) {
            return back()->withErrors(['code_or_link' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('vendor.vouchers.verify')
            ->with('success', __('vendor.vouchers.redeemed'))
            ->with('voucher_code', $voucher->code);
    }

    public function history(VoucherHistoryRequest $request)
    {
        $vendor = Auth::guard('vendor_web')->user();
        $vendor?->loadMissing('brand', 'branch');

        $filters = $request->validated();
        $history = $this->historyService->list($vendor, $filters);
        $branches = $vendor->role === 'VENDOR_ADMIN' ? $this->branchService->listBranches($vendor) : collect();

        return view('vendor.vouchers.history', [
            'vendor' => $vendor,
            'history' => $history,
            'branches' => $branches,
            'filters' => $filters,
        ]);
    }

    protected function resolveStatus($voucher): string
    {
        if (! $voucher) {
            return 'UNKNOWN';
        }

        if ($voucher->status === 'USED') {
            return 'USED';
        }

        if ($voucher->status === 'EXPIRED') {
            return 'EXPIRED';
        }

        if ($voucher->expires_at && $voucher->expires_at->isPast()) {
            return 'EXPIRED';
        }

        return 'VALID';
    }
}

