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
        $data = $request->only(['name_en','name_ar','type','is_active','sort_order']);
        $rc = $this->service->create($data);
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
        $rc = $this->service->update((int) $id, $request->only(['name_en','name_ar','type','is_active','sort_order']));
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
