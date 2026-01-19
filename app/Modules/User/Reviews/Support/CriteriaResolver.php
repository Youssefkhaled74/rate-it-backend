<?php

namespace App\Modules\User\Reviews\Support;

use App\Models\Branch;
use App\Models\RatingCriteria;

class CriteriaResolver
{
    /**
     * Return the rating criteria collection for the given branch.
     * This is the single source of truth used by both GET questions and POST review validation.
     *
     * @param Branch $branch
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForBranch(Branch $branch)
    {
        $subcategoryId = optional($branch->place)->subcategory_id;
        if (! $subcategoryId) {
            return collect();
        }

        $criteria = RatingCriteria::where('subcategory_id', $subcategoryId)
            ->with(['choices' => function ($q) { $q->orderBy('sort_order'); }])
            ->orderBy('sort_order')
            ->get();

        return $criteria;
    }
}
