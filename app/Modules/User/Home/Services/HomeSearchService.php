<?php

namespace App\Modules\User\Home\Services;

use App\Models\Brand;
use App\Models\Place;
use App\Models\Branch;
use Illuminate\Support\Str;

class HomeSearchService
{
    /**
     * Search across brands, places, and branches.
     *
     * @param string $q
     * @param int $limit
     * @param array|null $types
     * @return array
     */
    public function search(string $q, int $limit = 10, ?array $types = null): array
    {
        $q = trim(preg_replace('/\s+/', ' ', $q));
        $types = $types ?: ['brands', 'places', 'branches'];

        $pool = [];
        $perType = (int) ceil($limit / max(1, count($types)));

        if (in_array('brands', $types, true)) {
            $pool = array_merge($pool, $this->searchBrands($q, $perType));
        }

        if (in_array('places', $types, true)) {
            $pool = array_merge($pool, $this->searchPlaces($q, $perType));
        }

        if (in_array('branches', $types, true)) {
            $pool = array_merge($pool, $this->searchBranches($q, $perType));
        }

        // simple relevance: prefix matches first, then contains, then name asc
        usort($pool, function ($a, $b) use ($q) {
            $qa = Str::lower($q);
            $an = Str::lower($a['name']);
            $bn = Str::lower($b['name']);

            $aprefix = Str::startsWith($an, $qa) ? 0 : 1;
            $bprefix = Str::startsWith($bn, $qa) ? 0 : 1;
            if ($aprefix !== $bprefix) return $aprefix <=> $bprefix;

            $acontains = Str::contains($an, $qa) ? 0 : 1;
            $bcontains = Str::contains($bn, $qa) ? 0 : 1;
            if ($acontains !== $bcontains) return $acontains <=> $bcontains;

            return $an <=> $bn;
        });

        return array_slice($pool, 0, $limit);
    }

    protected function searchBrands(string $q, int $limit): array
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameCol = 'name_' . $locale;

        $qb = Brand::activeForUser()->select(['id', 'name_en', 'name_ar', 'logo', 'logo_url']);
        $qb->where($nameCol, 'like', "%{$q}%");
        $qb->orderByRaw("CASE WHEN {$nameCol} LIKE ? THEN 0 ELSE 1 END", ["{$q}%"])
            ->orderBy($nameCol);
        $rows = $qb->take($limit)->get();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'type' => 'brand',
                'id' => $r->id,
                'name' => $r->{$nameCol} ?? $r->name,
                'logo_url' => $r->logo ? asset($r->logo) : ($r->logo_url ? asset($r->logo_url) : null),
                'meta' => [],
            ];
        }

        return $out;
    }

    protected function searchPlaces(string $q, int $limit): array
    {
        $qb = Place::query()->select(['id', 'brand_id', 'name', 'city', 'area']);
        $qb->where('name', 'like', "%{$q}%");
        $qb->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["{$q}%"])->orderBy('name');
        $rows = $qb->take($limit)->get();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'type' => 'place',
                'id' => $r->id,
                'name' => $r->name,
                'logo_url' => null,
                'meta' => [
                    'brand_id' => $r->brand_id,
                    'city' => $r->city,
                    'area' => $r->area,
                ],
            ];
        }

        return $out;
    }

    protected function searchBranches(string $q, int $limit): array
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameCol = 'name_' . $locale;

        $qb = Branch::query()->select(['id', 'place_id', 'name', 'name_en', 'name_ar', 'address']);
        $qb->where(function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                ->orWhere('name_en', 'like', "%{$q}%")
                ->orWhere('name_ar', 'like', "%{$q}%");
        });
        $qb->orderByRaw("CASE WHEN {$nameCol} LIKE ? THEN 0 ELSE 1 END", ["{$q}%"])->orderBy($nameCol);
        $rows = $qb->take($limit)->get();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'type' => 'branch',
                'id' => $r->id,
                'name' => $r->{$nameCol} ?? $r->name,
                'logo_url' => null,
                'meta' => [
                    'place_id' => $r->place_id,
                    'address' => $r->address,
                ],
            ];
        }

        return $out;
    }
}
