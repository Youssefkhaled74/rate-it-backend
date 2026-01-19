<?php

namespace App\Modules\Admin\Catalog\Services;

use App\Models\RatingCriteriaChoice;

class RatingCriteriaChoiceService
{
    public function list(int $criteriaId, array $filters = [])
    {
        $query = RatingCriteriaChoice::where('rating_criteria_id', $criteriaId);
        if (isset($filters['active'])) {
            $query->where('is_active', (bool) $filters['active']);
        }
        return $query->orderBy('sort_order')->get();
    }

    public function create(int $criteriaId, array $data): RatingCriteriaChoice
    {
        $data['rating_criteria_id'] = $criteriaId;
        return RatingCriteriaChoice::create($data);
    }

    public function find(int $id): ?RatingCriteriaChoice
    {
        return RatingCriteriaChoice::find($id);
    }

    public function update(int $id, array $data): ?RatingCriteriaChoice
    {
        $choice = RatingCriteriaChoice::find($id);
        if (! $choice) return null;
        $choice->update($data);
        return $choice;
    }

    public function delete(int $id): bool
    {
        $choice = RatingCriteriaChoice::find($id);
        if (! $choice) return false;
        return (bool) $choice->delete();
    }
}
