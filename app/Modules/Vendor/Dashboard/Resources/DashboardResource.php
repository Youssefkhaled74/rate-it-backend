<?php

namespace App\Modules\Vendor\Dashboard\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'total_branches' => $this['total_branches'],
            'reviews_count' => [
                'last_7_days' => $this['reviews_count']['last_7_days'],
                'last_30_days' => $this['reviews_count']['last_30_days'],
            ],
            'average_rating_brand' => $this['average_rating_brand'],
            'top_branches_by_rating' => $this['top_branches_by_rating'],
            'vouchers_used' => [
                'last_7_days' => $this['vouchers_used']['last_7_days'],
                'last_30_days' => $this['vouchers_used']['last_30_days'],
            ],
        ];
    }
}
