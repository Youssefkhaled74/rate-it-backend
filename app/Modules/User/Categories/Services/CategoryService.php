<?php

namespace App\Modules\User\Categories\Services;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Str;

class CategoryService
{
    public function listCategories(?string $q, int $limit)
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameCol = 'name_' . $locale;

        $query = Category::query()->where('is_active', true);

        if ($q) {
            $query->where($nameCol, 'like', '%' . $q . '%');
        }

        $query->orderByRaw("CASE WHEN {$nameCol} LIKE ? THEN 0 ELSE 1 END", [$q ? $q . '%' : ''])
            ->orderBy($nameCol)
            ->limit($limit);

        return $query->get();
    }

    public function listSubcategories(int $categoryId, ?string $q, int $limit)
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameCol = 'name_' . $locale;

        $query = Subcategory::query()->where('category_id', $categoryId)->where('is_active', true);

        if ($q) {
            $query->where($nameCol, 'like', '%' . $q . '%');
        }

        $query->orderByRaw("CASE WHEN {$nameCol} LIKE ? THEN 0 ELSE 1 END", [$q ? $q . '%' : ''])
            ->orderBy($nameCol)
            ->limit($limit);

        return $query->get();
    }

    public function search(string $q, ?int $categoryId, array $types, int $limit)
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $nameCol = 'name_' . $locale;

        $results = [];

        $perType = max(1, (int) floor($limit / max(1, count($types))));

        if (in_array('categories', $types, true)) {
            $catQuery = Category::query()->where('is_active', true);
            if ($categoryId) {
                $catQuery->where('id', $categoryId);
            }
            $catQuery->where($nameCol, 'like', '%' . $q . '%')
                ->orderByRaw("CASE WHEN {$nameCol} LIKE ? THEN 0 ELSE 1 END", [$q . '%'])
                ->orderBy($nameCol)
                ->limit($perType);

            $cats = $catQuery->get();
            foreach ($cats as $c) {
                $results[] = [
                    'type' => 'category',
                    'id' => $c->id,
                    'name' => $c->{$nameCol},
                    'logo' => $c->logo,
                ];
            }
        }

        if (in_array('subcategories', $types, true)) {
            $subQuery = Subcategory::query()->where('is_active', true)->where($nameCol, 'like', '%' . $q . '%');
            if ($categoryId) {
                $subQuery->where('category_id', $categoryId);
            }
            $subQuery->orderByRaw("CASE WHEN {$nameCol} LIKE ? THEN 0 ELSE 1 END", [$q . '%'])
                ->orderBy($nameCol)
                ->limit($perType);

            $subs = $subQuery->get();
            foreach ($subs as $s) {
                $results[] = [
                    'type' => 'subcategory',
                    'id' => $s->id,
                    'category_id' => $s->category_id,
                    'name' => $s->{$nameCol},
                    'image' => $s->image,
                ];
            }
        }

        // final sorting: prefix matches first, then contains, then name asc
        usort($results, function ($a, $b) use ($q) {
            $qa = stripos($a['name'], $q) === 0 ? 0 : 1;
            $qb = stripos($b['name'], $q) === 0 ? 0 : 1;
            if ($qa !== $qb) {
                return $qa <=> $qb;
            }
            return strcasecmp($a['name'], $b['name']);
        });

        return [
            'query' => $q,
            'results' => array_slice($results, 0, $limit),
        ];
    }
}
