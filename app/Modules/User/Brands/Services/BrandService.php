<?php

namespace App\Modules\User\Brands\Services;

use App\Models\Brand;
use App\Models\Place;
use Illuminate\Support\Facades\DB;

class BrandService
{
    public function getBrandDetails(int $brandId)
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameCol = 'name_' . $locale;
        $descCol = 'description_' . $locale;

        $brand = Brand::activeForUser()->where('id', $brandId)->firstOrFail();

        // aggregate ratings from places
        $stats = DB::table('places')
            ->join('reviews', 'places.id', '=', 'reviews.place_id')
            ->where('places.brand_id', $brandId)
            ->where('reviews.status', 'ACTIVE')
            ->selectRaw('AVG(reviews.overall_rating) as avg_rating, COUNT(reviews.id) as reviews_count')
            ->first();

        return [
            'brand' => $brand,
            'avg_rating' => $stats->avg_rating ? round($stats->avg_rating, 1) : null,
            'reviews_count' => (int) ($stats->reviews_count ?? 0),
        ];
    }

    public function listBrandPlaces(int $brandId)
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameCol = 'name_' . $locale;

        Brand::activeForUser()->where('id', $brandId)->firstOrFail();
        $places = Place::where('brand_id', $brandId)->where('is_active', true)->get();

        // attach ratings
        $placeIds = $places->pluck('id')->toArray();
        $ratings = DB::table('reviews')
            ->selectRaw('place_id, AVG(overall_rating) as avg_rating, COUNT(id) as reviews_count')
            ->whereIn('place_id', $placeIds)
            ->where('status', 'ACTIVE')
            ->groupBy('place_id')
            ->get()
            ->keyBy('place_id');

        return $places->map(function ($p) use ($ratings, $locale) {
            $name = $p->{'name_' . $locale} ?? $p->name;
            $r = $ratings[$p->id] ?? null;
            return [
                'place' => $p,
                'name' => $name,
                'avg_rating' => $r ? round($r->avg_rating, 1) : null,
                'reviews_count' => $r ? (int) $r->reviews_count : 0,
            ];
        });
    }
}
