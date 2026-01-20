<?php

namespace App\Modules\Admin\Catalog\Services;

use App\Models\RatingCriteriaChoice;

class RatingCriteriaChoiceService
{
    public function list(int $criteriaId, array $filters = [])
    {
        $query = RatingCriteriaChoice::where('criteria_id', $criteriaId);
        if (isset($filters['active'])) {
            $query->where('is_active', (bool) $filters['active']);
        }
        return $query->orderBy('sort_order')->get();
    }

    public function create(int $criteriaId, array $data): RatingCriteriaChoice
    {
        $criteria = \App\Models\RatingCriteria::find($criteriaId);
        if (! $criteria) throw new \RuntimeException('rating_criteria.not_found');
        if ($criteria->type !== 'MULTIPLE_CHOICE') {
            throw new \RuntimeException('rating_criteria.choice_type_invalid');
        }
        // map name_en -> choice_text if present
        if (isset($data['name_en'])) {
            $data['choice_text'] = $data['name_en'];
            unset($data['name_en']);
        }
        $data['criteria_id'] = $criteriaId;
        // enforce unique value per criteria if provided
        if (array_key_exists('value', $data) && $data['value'] !== null) {
            $exists = RatingCriteriaChoice::where('criteria_id', $criteriaId)->where('value', $data['value'])->exists();
            if ($exists) throw new \RuntimeException('rating_criteria.choice_value_duplicate');
        }
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
        if (isset($data['name_en'])) {
            $data['choice_text'] = $data['name_en'];
            unset($data['name_en']);
        }
        if (array_key_exists('value', $data) && $data['value'] !== null) {
            $exists = RatingCriteriaChoice::where('criteria_id', $choice->criteria_id)
                ->where('value', $data['value'])
                ->where('id', '!=', $id)
                ->exists();
            if ($exists) throw new \RuntimeException('rating_criteria.choice_value_duplicate');
        }
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
