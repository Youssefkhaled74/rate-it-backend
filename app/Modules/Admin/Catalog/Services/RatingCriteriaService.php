<?php

namespace App\Modules\Admin\Catalog\Services;

use App\Models\RatingCriteria;
use App\Models\Subcategory;

class RatingCriteriaService
{
    private const ALLOWED_TYPES = ['RATING','YES_NO','MULTIPLE_CHOICE'];

    public function list(array $filters = [])
    {
        $query = RatingCriteria::with('subcategory', 'choices', 'reviewAnswers');
        if (isset($filters['active'])) {
            $query->where('is_active', (bool) $filters['active']);
        }
        if (isset($filters['type'])) {
            $query->where('type', strtoupper($filters['type']));
        }
        if (isset($filters['subcategory_id'])) {
            $query->where('subcategory_id', (int) $filters['subcategory_id']);
        }
        return $query->orderBy('subcategory_id')->orderBy('sort_order')->get();
    }

    public function create(array $data): RatingCriteria
    {
        // Service-level validations
        $type = strtoupper($data['type'] ?? '');
        if (! in_array($type, self::ALLOWED_TYPES, true)) {
            throw new \RuntimeException('rating_criteria.invalid_type');
        }
        if (empty($data['subcategory_id']) || ! Subcategory::where('id', $data['subcategory_id'])->exists()) {
            throw new \RuntimeException('rating_criteria.invalid_subcategory');
        }
        $data['type'] = $type;
        return RatingCriteria::create($data);
    }

    public function find(int $id): ?RatingCriteria
    {
        return RatingCriteria::with('subcategory', 'choices', 'reviewAnswers')->find($id);
    }

    public function update(int $id, array $data): ?RatingCriteria
    {
        $rc = RatingCriteria::find($id);
        if (! $rc) return null;

        // If changing type away from MULTIPLE_CHOICE while choices exist, block
        if (array_key_exists('type', $data)) {
            $newType = strtoupper($data['type'] ?? '');
            if (! in_array($newType, self::ALLOWED_TYPES, true)) {
                throw new \RuntimeException('rating_criteria.invalid_type');
            }
            if ($newType !== 'MULTIPLE_CHOICE' && $rc->choices()->exists()) {
                throw new \RuntimeException('rating_criteria.change_type_has_choices');
            }
            $data['type'] = $newType;
        }

        if (array_key_exists('subcategory_id', $data) && $data['subcategory_id']) {
            if (! Subcategory::where('id', $data['subcategory_id'])->exists()) {
                throw new \RuntimeException('rating_criteria.invalid_subcategory');
            }
        }

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
