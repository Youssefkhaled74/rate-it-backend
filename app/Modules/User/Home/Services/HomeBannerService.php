<?php

namespace App\Modules\User\Home\Services;

use App\Models\Banner;
use Illuminate\Support\Facades\DB;

class HomeBannerService
{
    public function listForHome()
    {
        return Banner::query()
            ->with(['brand:id,name_en,name_ar,logo'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhereDate('start_date', '<=', now()->toDateString());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', now()->toDateString());
            })
            ->orderByDesc('id')
            ->select([
                'id',
                'brand_id',
                'offer_name as title',
                DB::raw('NULL as body'),
                'image',
                DB::raw("CASE WHEN brand_id IS NULL THEN NULL ELSE 'brand' END as action_type"),
                DB::raw('brand_id as action_value'),
                DB::raw('id as sort_order'),
            ])
            ->get();
    }
}
