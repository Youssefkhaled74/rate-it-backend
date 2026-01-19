<?php

namespace App\Modules\Admin\Catalog\Services;

use App\Models\Branch;

class BranchService
{
    public function list(array $filters = [])
    {
        $query = Branch::query();
        if (isset($filters['place_id'])) {
            $query->where('place_id', $filters['place_id']);
        }
        if (isset($filters['active'])) {
            $query->where('is_active', (bool) $filters['active']);
        }
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function create(array $data): Branch
    {
        return Branch::create($data);
    }

    public function find(int $id): ?Branch
    {
        return Branch::find($id);
    }

    public function update(int $id, array $data): ?Branch
    {
        $branch = Branch::find($id);
        if (! $branch) return null;
        $branch->update($data);
        return $branch;
    }

    public function delete(int $id): bool
    {
        $branch = Branch::find($id);
        if (! $branch) return false;
        return (bool) $branch->delete();
    }
}
