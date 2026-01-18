<?php

namespace App\Modules\User\Brands\Services;

use App\Models\Place;
use Illuminate\Support\Facades\DB;

class PlaceService
{
    public function getPlaceDetails(int $placeId)
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameCol = 'name_' . $locale;
        $descCol = 'description_' . $locale;

        $place = Place::where('id', $placeId)->where('is_active', true)->firstOrFail();

        $stats = DB::table('reviews')
            ->where('place_id', $placeId)
            ->where('status', 'ACTIVE')
            ->selectRaw('AVG(overall_rating) as avg_rating, COUNT(id) as reviews_count')
            ->first();

        $reviews = DB::table('reviews')
            ->where('place_id', $placeId)
            ->where('status', 'ACTIVE')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $primaryBranch = DB::table('branches')->where('place_id', $placeId)->orderBy('id')->first();

        return [
            'place' => $place,
            'avg_rating' => $stats->avg_rating ? round($stats->avg_rating, 1) : null,
            'reviews_count' => (int) ($stats->reviews_count ?? 0),
            'reviews' => $reviews,
            'primary_branch' => $primaryBranch,
        ];
    }

    public function listPlaceReviews(int $placeId, int $perPage = 10)
    {
        return DB::table('reviews')
            ->where('place_id', $placeId)
            ->where('status', 'ACTIVE')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
