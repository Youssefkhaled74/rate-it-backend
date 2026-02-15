<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Modules\Vendor\Dashboard\Services\VendorDashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // SRS Notes: see docs/vendor_panel_notes.md (sections 2.4, 8.3, 14).

    public function home()
    {
        $vendor = Auth::guard('vendor_web')->user();
        if (! $vendor) {
            return redirect()->route('vendor.login');
        }

        if ($vendor->role === 'BRANCH_STAFF') {
            return redirect()->route('vendor.vouchers.verify');
        }

        return redirect()->route('vendor.dashboard');
    }

    public function index(VendorDashboardService $service)
    {
        $vendor = Auth::guard('vendor_web')->user();
        $vendor?->loadMissing('brand', 'branch');

        $summary = $service->getSummary($vendor);

        return view('vendor.dashboard.index', [
            'vendor' => $vendor,
            'summary' => $summary,
        ]);
    }
}

