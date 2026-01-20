<?php

namespace App\Modules\Admin\Catalog\Services;

use App\Models\RatingCriteria;
use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;

class SubcategoryCriteriaService
{
    public function list(int $subcategoryId)
    {
        return RatingCriteria::where('subcategory_id', $subcategoryId)
            ->with('choices')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Sync the provided list of criteria for the given subcategory.
     * This will move existing criteria into the subcategory, update is_required/sort_order,
     * and remove criteria that previously belonged to the subcategory but are not present.
     */
    public function sync(int $subcategoryId, array $items): void
    {
        DB::transaction(function () use ($subcategoryId, $items) {
            $ids = array_column($items, 'criteria_id');

            // Validate criteria exist
            $found = RatingCriteria::whereIn('id', $ids)->pluck('id')->toArray();
            $missing = array_diff($ids, $found);
            if (! empty($missing)) {
                throw new \RuntimeException('rating_criteria.not_found');
            }

            // Update or move provided criteria into this subcategory
            foreach ($items as $item) {
                $rc = RatingCriteria::find($item['criteria_id']);
                $rc->subcategory_id = $subcategoryId;
                if (array_key_exists('is_required', $item)) {
                    $rc->is_required = (bool) $item['is_required'];
                }
                if (array_key_exists('sort_order', $item)) {
                    $rc->sort_order = (int) $item['sort_order'];
                }
                $rc->save();
            }

            // Delete criteria that used to belong to this subcategory but were not included
            $keep = $ids;
            RatingCriteria::where('subcategory_id', $subcategoryId)
                ->whereNotIn('id', $keep)
                ->get()
                ->each(function ($c) { $c->delete(); });
        });
    }

    public function detach(int $subcategoryId, int $criteriaId): void
    {
        $rc = RatingCriteria::find($criteriaId);
        if (! $rc || $rc->subcategory_id !== $subcategoryId) {
            throw new \RuntimeException('rating_criteria.not_found_for_subcategory');
        }

        // Deleting the criteria as it cannot exist without a subcategory in current schema
        $rc->delete();
    }

    public function reorder(int $subcategoryId, array $criteriaIds): void
    {
        DB::transaction(function () use ($subcategoryId, $criteriaIds) {
            $existing = RatingCriteria::where('subcategory_id', $subcategoryId)
                ->whereIn('id', $criteriaIds)
                ->pluck('id')
                ->toArray();

            $missing = array_diff($criteriaIds, $existing);
            if (! empty($missing)) {
                throw new \RuntimeException('rating_criteria.reorder_invalid_ids');
            }

            foreach ($criteriaIds as $i => $id) {
                RatingCriteria::where('id', $id)->update(['sort_order' => $i]);
            }
        });
    }
}
