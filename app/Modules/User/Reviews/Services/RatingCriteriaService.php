<?php

namespace App\Modules\User\Reviews\Services;

use App\Models\Branch;
use App\Models\RatingCriteria;

class RatingCriteriaService
{
    public function getQuestionsForBranch(Branch $branch)
    {
        $subcategoryId = optional($branch->place)->subcategory_id;
        if (! $subcategoryId) {
            return [];
        }

        $criteria = RatingCriteria::where('subcategory_id', $subcategoryId)
            ->orderBy('sort_order')
            ->with(['choices' => function($q){ $q->orderBy('sort_order'); }])
            ->get();

        return $criteria;
    }
}
