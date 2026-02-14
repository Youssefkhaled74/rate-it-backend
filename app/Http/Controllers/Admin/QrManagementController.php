<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use Illuminate\Http\Request;

class QrManagementController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $brandId = (int) $request->get('brand_id', 0);
        $status = $request->get('status', '');

        $query = Branch::query()
            ->with(['brand:id,name_en,name_ar'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('name_en', 'like', "%{$q}%")
                   ->orWhere('name_ar', 'like', "%{$q}%")
                   ->orWhere('address', 'like', "%{$q}%")
                   ->orWhereHas('brand', function ($b) use ($q) {
                       $b->where('name_en', 'like', "%{$q}%")
                         ->orWhere('name_ar', 'like', "%{$q}%");
                   });
            })
            ->when($brandId > 0, function ($qq) use ($brandId) {
                $qq->where('brand_id', $brandId);
            })
            ->when($status !== '', function ($qq) use ($status) {
                $qq->where('is_active', (bool) $status);
            })
            ->orderByDesc('qr_generated_at')
            ->orderByDesc('id');

        $branches = $query->paginate(15)->withQueryString();
        $brands = Brand::query()->orderBy('name_en')->get();

        $stats = [
            'total' => (int) Branch::count(),
            'active' => (int) Branch::where('is_active', true)->count(),
            'inactive' => (int) Branch::where('is_active', false)->count(),
        ];

        return view('admin.qr-management.index', compact('branches', 'q', 'brandId', 'status', 'brands', 'stats'));
    }

    public function exportCsv(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $brandId = (int) $request->get('brand_id', 0);
        $status = $request->get('status', '');

        $query = Branch::query()
            ->with(['brand:id,name_en,name_ar'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('name_en', 'like', "%{$q}%")
                   ->orWhere('name_ar', 'like', "%{$q}%")
                   ->orWhere('address', 'like', "%{$q}%")
                   ->orWhereHas('brand', function ($b) use ($q) {
                       $b->where('name_en', 'like', "%{$q}%")
                         ->orWhere('name_ar', 'like', "%{$q}%");
                   });
            })
            ->when($brandId > 0, function ($qq) use ($brandId) {
                $qq->where('brand_id', $brandId);
            })
            ->when($status !== '', function ($qq) use ($status) {
                $qq->where('is_active', (bool) $status);
            })
            ->orderByDesc('qr_generated_at')
            ->orderByDesc('id');

        $fileName = 'qr-management-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        $callback = function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Branch', 'Address', 'Brand', 'City', 'Area', 'QR Code', 'Generated At', 'Active']);
            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $b) {
                    fputcsv($out, [
                        $b->name_en ?? $b->name_ar ?? $b->name ?? '-',
                        $b->address ?? '-',
                        $b->brand?->name_en ?? $b->brand?->name_ar ?? '-',
                        $b->city_id ?? '-',
                        $b->area_id ?? '-',
                        $b->qr_code_value ?? '-',
                        optional($b->qr_generated_at)->format('Y-m-d H:i'),
                        $b->is_active ? 'yes' : 'no',
                    ]);
                }
            });
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
