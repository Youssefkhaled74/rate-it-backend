<?php

namespace App\Modules\Admin\Catalog\Controllers;

use App\Support\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Models\RatingCriteria;
use App\Models\RatingCriteriaChoice;
use App\Modules\Admin\Catalog\Requests\StoreRatingCriteriaChoiceRequest;
use App\Modules\Admin\Catalog\Requests\UpdateRatingCriteriaChoiceRequest;
use App\Modules\Admin\Catalog\Resources\RatingCriteriaChoiceResource;
use App\Modules\Admin\Catalog\Services\RatingCriteriaService;
use App\Modules\Admin\Catalog\Services\RatingCriteriaChoiceService;

class RatingCriteriaChoicesController extends BaseApiController
{
    protected RatingCriteriaService $criteriaService;
    protected RatingCriteriaChoiceService $choiceService;

    public function __construct(RatingCriteriaService $criteriaService, RatingCriteriaChoiceService $choiceService)
    {
        $this->criteriaService = $criteriaService;
        $this->choiceService = $choiceService;
    }

    public function index($criteria_id, Request $request)
    {
        $criteria = $this->criteriaService->find((int) $criteria_id);
        if (! $criteria) return $this->error('Criteria not found', null, 404);
        $filters = [];
        if ($request->has('active')) $filters['active'] = $request->query('active');
        $items = $this->choiceService->list((int) $criteria_id, $filters);
        return $this->success(RatingCriteriaChoiceResource::collection($items));
    }

    public function store($criteria_id, StoreRatingCriteriaChoiceRequest $request)
    {
        $criteria = $this->criteriaService->find((int) $criteria_id);
        if (! $criteria) return $this->error('Criteria not found', null, 404);
        $data = $request->only(['name_en','name_ar','value','is_active','sort_order']);
        $choice = $this->choiceService->create((int) $criteria_id, $data);
        return $this->created(new RatingCriteriaChoiceResource($choice));
    }

    public function update($criteria_id, $choice_id, UpdateRatingCriteriaChoiceRequest $request)
    {
        $criteria = $this->criteriaService->find((int) $criteria_id);
        if (! $criteria) return $this->error('Criteria not found', null, 404);
        $choice = $this->choiceService->find((int) $choice_id);
        if (! $choice) return $this->error('Choice not found', null, 404);
        $choice = $this->choiceService->update((int) $choice_id, $request->only(['name_en','name_ar','value','is_active','sort_order']));
        return $this->success(new RatingCriteriaChoiceResource($choice));
    }

    public function destroy($criteria_id, $choice_id)
    {
        $criteria = $this->criteriaService->find((int) $criteria_id);
        if (! $criteria) return $this->error('Criteria not found', null, 404);
        $ok = $this->choiceService->delete((int) $choice_id);
        if (! $ok) return $this->error('Choice not found', null, 404);
        return $this->noContent('rating_criteria.choice_deleted');
    }
}
