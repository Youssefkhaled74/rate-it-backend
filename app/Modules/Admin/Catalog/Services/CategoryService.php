<?php

namespace App\Modules\Admin\Catalog\Services;

use App\Models\Category;

class CategoryService
{
    public function list(array $filters = [])
    {
        $query = Category::query();
        if (isset($filters['active'])) {
            $query->where('is_active', (bool) $filters['active']);
        }
        return $query->orderBy('sort_order')->get();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function find(int $id): ?Category
    {
        return Category::find($id);
    }

    public function update(int $id, array $data): ?Category
    {
        $cat = Category::find($id);
        if (! $cat) return null;
        $cat->update($data);
        return $cat;
    }

    public function delete(int $id): bool
    {
        $cat = Category::find($id);
        if (! $cat) return false;
        return (bool) $cat->delete();
    }
}
