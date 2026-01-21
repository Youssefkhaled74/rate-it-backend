<?php

namespace App\Modules\Admin\Catalog\Controllers;

use App\Support\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Models\RatingCriteria;
use App\Modules\Admin\Catalog\Requests\StoreRatingCriteriaRequest;
use App\Modules\Admin\Catalog\Requests\UpdateRatingCriteriaRequest;
use App\Modules\Admin\Catalog\Resources\RatingCriteriaResource;
use App\Modules\Admin\Catalog\Services\RatingCriteriaService;

class RatingCriteriaController extends BaseApiController
{
    protected RatingCriteriaService $service;

    public function __construct(RatingCriteriaService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = [];
        if ($request->has('active')) {
            $filters['active'] = $request->query('active');
        }
        $items = $this->service->list($filters);
        return $this->success(RatingCriteriaResource::collection($items), 'rating_criteria.list');
    }

    public function store(StoreRatingCriteriaRequest $request)
    {
        $data = $request->only(['name_en','name_ar','type','is_active','sort_order','subcategory_id','is_required']);
        // Map name_en/name_ar to question_text/question_en/question_ar
        $data['question_text'] = $data['name_en'] ?? $data['name_ar'] ?? '';
        $data['question_en'] = $data['name_en'] ?? '';
        $data['question_ar'] = $data['name_ar'] ?? null;
        unset($data['name_en'], $data['name_ar']);
        try {
            $rc = $this->service->create($data);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, 422);
        }
        return $this->created(new RatingCriteriaResource($rc), 'rating_criteria.created');
    }

    public function show($id)
    {
        $rc = $this->service->find((int) $id);
        if (! $rc) return $this->error('Not found', null, 404);
        return $this->success(new RatingCriteriaResource($rc));
    }

    public function update(UpdateRatingCriteriaRequest $request, $id)
    {
        $data = $request->only(['name_en','name_ar','type','is_active','sort_order','subcategory_id','is_required']);
        // Map name_en/name_ar to question_text/question_en/question_ar if provided
        if (isset($data['name_en']) || isset($data['name_ar'])) {
            $data['question_text'] = $data['name_en'] ?? $data['name_ar'] ?? '';
            $data['question_en'] = $data['name_en'] ?? null;
            $data['question_ar'] = $data['name_ar'] ?? null;
            unset($data['name_en'], $data['name_ar']);
        }
        try {
            $rc = $this->service->update((int) $id, $data);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, 422);
        }
        if (! $rc) return $this->error('Not found', null, 404);
        return $this->success(new RatingCriteriaResource($rc), 'rating_criteria.updated');
    }

    public function destroy($id)
    {
        $ok = $this->service->delete((int) $id);
        if (! $ok) return $this->error('Not found', null, 404);
        return $this->noContent('rating_criteria.deleted');
    }
}
