<?php

namespace App\Modules\User\Reviews\Services;

use App\Models\Branch;
use App\Models\RatingCriteria;
use App\Modules\User\Reviews\Support\CriteriaResolver;

class RatingCriteriaService
{
    public function getQuestionsForBranch(Branch $branch)
    {
        $resolver = app(CriteriaResolver::class);
        return $resolver->getForBranch($branch);
    }
}
