<?php

namespace App\Modules\Admin\Catalog\Services;

use App\Models\Subcategory;
use App\Models\RatingCriteria;
use App\Models\Place;
use App\Models\Branch;
use App\Models\Brand;

class SubcategoryService
{
    public function list(array $filters = [])
    {
        $query = Subcategory::query();
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (isset($filters['active'])) {
            $query->where('is_active', (bool) $filters['active']);
        }
        return $query->orderBy('sort_order')->get();
    }

    public function create(array $data): Subcategory
    {
        return Subcategory::create($data);
    }

    public function find(int $id): ?Subcategory
    {
        return Subcategory::find($id);
    }

    public function update(int $id, array $data): ?Subcategory
    {
        $sub = Subcategory::find($id);
        if (! $sub) return null;
        $sub->update($data);
        return $sub;
    }

    public function delete(int $id): bool
    {
        $sub = Subcategory::find($id);
        if (! $sub) return false;

        // Prevent deletion if there are rating criteria attached
        if (RatingCriteria::where('subcategory_id', $id)->exists()) {
            throw new \RuntimeException('subcategory.delete_blocked.criteria');
        }

        // Prevent deletion if any place references this subcategory
        if (Place::where('subcategory_id', $id)->exists()) {
            throw new \RuntimeException('subcategory.delete_blocked.places');
        }

        // Prevent deletion if any brand references this subcategory
        if (Brand::where('subcategory_id', $id)->exists()) {
            throw new \RuntimeException('subcategory.delete_blocked.brands');
        }

        // Prevent deletion if any branch exists under places of this subcategory
        $placeIds = Place::where('subcategory_id', $id)->pluck('id');
        if ($placeIds->isNotEmpty() && Branch::whereIn('place_id', $placeIds)->exists()) {
            throw new \RuntimeException('subcategory.delete_blocked.branches');
        }

        return (bool) $sub->delete();
    }
}
