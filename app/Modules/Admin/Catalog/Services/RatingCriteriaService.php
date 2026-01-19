<?php

namespace App\Modules\Admin\Catalog\Services;

use App\Models\RatingCriteria;

class RatingCriteriaService
{
    public function list(array $filters = [])
    {
        $query = RatingCriteria::query();
        if (isset($filters['active'])) {
            $query->where('is_active', (bool) $filters['active']);
        }
        return $query->orderBy('sort_order')->get();
    }

    public function create(array $data): RatingCriteria
    {
        return RatingCriteria::create($data);
    }

    public function find(int $id): ?RatingCriteria
    {
        return RatingCriteria::find($id);
    }

    public function update(int $id, array $data): ?RatingCriteria
    {
        $rc = RatingCriteria::find($id);
        if (! $rc) return null;
        $rc->update($data);
        return $rc;
    }

    public function delete(int $id): bool
    {
        $rc = RatingCriteria::find($id);
        if (! $rc) return false;
        return (bool) $rc->delete();
    }
}
