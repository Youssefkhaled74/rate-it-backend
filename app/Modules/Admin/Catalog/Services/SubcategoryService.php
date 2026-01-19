<?php

namespace App\Modules\Admin\Catalog\Services;

use App\Models\Subcategory;

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
        return (bool) $sub->delete();
    }
}
