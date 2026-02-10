<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VouchersController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = (string) $request->get('status', '');
        $brandId = (int) $request->get('brand_id', 0);
        $from = $request->get('from');
        $to = $request->get('to');

        $query = Voucher::query()
            ->with(['user:id,name,phone', 'brand:id,name_en,name_ar', 'usedBranch:id,name', 'verifiedByVendor:id,name'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('code', 'like', "%{$q}%")
                   ->orWhereHas('user', function ($u) use ($q) {
                       $u->where('name', 'like', "%{$q}%")
                         ->orWhere('phone', 'like', "%{$q}%");
                   });
            })
            ->when($status !== '', function ($qq) use ($status) {
                $qq->where('status', $status);
            })
            ->when($brandId > 0, function ($qq) use ($brandId) {
                $qq->where('brand_id', $brandId);
            });

        if ($from) {
            $query->where('issued_at', '>=', Carbon::parse($from)->startOfDay());
        }
        if ($to) {
            $query->where('issued_at', '<=', Carbon::parse($to)->endOfDay());
        }

        $vouchers = $query->orderBy('issued_at', 'desc')->paginate(15)->withQueryString();

        $statsQuery = clone $query;
        $stats = [
            'total' => (int) $statsQuery->count(),
            'valid' => (int) (clone $statsQuery)->where('status', 'VALID')->count(),
            'used' => (int) (clone $statsQuery)->where('status', 'USED')->count(),
            'expired' => (int) (clone $statsQuery)->where('status', 'EXPIRED')->count(),
            'points_used' => (int) (clone $statsQuery)->sum('points_used'),
            'value_amount' => (float) (clone $statsQuery)->sum('value_amount'),
        ];

        $brands = Brand::query()->orderBy('name_en')->get();

        return view('admin.vouchers.index', compact('vouchers', 'q', 'status', 'brandId', 'from', 'to', 'stats', 'brands'));
    }

    public function exportCsv(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = (string) $request->get('status', '');
        $brandId = (int) $request->get('brand_id', 0);
        $from = $request->get('from');
        $to = $request->get('to');

        $query = Voucher::query()
            ->with(['user:id,name,phone', 'brand:id,name_en,name_ar', 'usedBranch:id,name'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('code', 'like', "%{$q}%")
                   ->orWhereHas('user', function ($u) use ($q) {
                       $u->where('name', 'like', "%{$q}%")
                         ->orWhere('phone', 'like', "%{$q}%");
                   });
            })
            ->when($status !== '', function ($qq) use ($status) {
                $qq->where('status', $status);
            })
            ->when($brandId > 0, function ($qq) use ($brandId) {
                $qq->where('brand_id', $brandId);
            });

        if ($from) {
            $query->where('issued_at', '>=', Carbon::parse($from)->startOfDay());
        }
        if ($to) {
            $query->where('issued_at', '<=', Carbon::parse($to)->endOfDay());
        }

        $fileName = 'vouchers-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        $callback = function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Code', 'Status', 'User', 'Phone', 'Brand', 'Points Used', 'Value', 'Issued At', 'Used At', 'Expires At', 'Used Branch']);
            $query->orderBy('issued_at', 'desc')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $v) {
                    fputcsv($out, [
                        $v->code,
                        $v->status,
                        $v->user?->name ?? '-',
                        $v->user?->phone ?? '-',
                        $v->brand?->name_en ?? '-',
                        $v->points_used,
                        $v->value_amount,
                        optional($v->issued_at)->format('Y-m-d H:i'),
                        optional($v->used_at)->format('Y-m-d H:i'),
                        optional($v->expires_at)->format('Y-m-d H:i'),
                        $v->usedBranch?->name ?? '-',
                    ]);
                }
            });
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
