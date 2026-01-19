<?php

namespace App\Modules\Admin\Catalog\Services;

use App\Models\Brand;

class BrandService
{
    public function list(array $filters = [])
    {
        $query = Brand::query();
        if (isset($filters['active'])) {
            $query->where('is_active', (bool) $filters['active']);
        }
        return $query->orderBy('sort_order')->get();
    }

    public function create(array $data): Brand
    {
        return Brand::create($data);
    }

    public function find(int $id): ?Brand
    {
        return Brand::find($id);
    }

    public function update(int $id, array $data): ?Brand
    {
        $brand = Brand::find($id);
        if (! $brand) return null;
        $brand->update($data);
        return $brand;
    }

    public function delete(int $id): bool
    {
        $brand = Brand::find($id);
        if (! $brand) return false;
        return (bool) $brand->delete();
    }
}
