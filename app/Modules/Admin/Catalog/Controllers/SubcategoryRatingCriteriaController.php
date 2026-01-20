<?php

namespace App\Modules\Admin\Catalog\Controllers;

use App\Support\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Modules\Admin\Catalog\Services\SubcategoryCriteriaService;
use App\Modules\Admin\Catalog\Resources\SubcategoryCriteriaResource;
use App\Modules\Admin\Catalog\Requests\SyncSubcategoryCriteriaRequest;
use App\Modules\Admin\Catalog\Requests\ReorderSubcategoryCriteriaRequest;

class SubcategoryRatingCriteriaController extends BaseApiController
{
    protected SubcategoryCriteriaService $service;

    public function __construct(SubcategoryCriteriaService $service)
    {
        $this->service = $service;
    }

    public function index($id)
    {
        $items = $this->service->list((int) $id);
        return $this->success(SubcategoryCriteriaResource::collection($items), 'rating_criteria.list');
    }

    public function sync(SyncSubcategoryCriteriaRequest $request, $id)
    {
        $data = $request->input('items', []);
        try {
            $this->service->sync((int) $id, $data);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, 422);
        }
        return $this->success([], 'rating_criteria.synced');
    }

    public function destroy($id, $criteria_id)
    {
        try {
            $this->service->detach((int) $id, (int) $criteria_id);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, 404);
        }
        return $this->noContent('rating_criteria.detached');
    }

    public function reorder(ReorderSubcategoryCriteriaRequest $request, $id)
    {
        $criteriaIds = $request->input('criteria_ids', []);
        try {
            $this->service->reorder((int) $id, $criteriaIds);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, 422);
        }
        return $this->success([], 'rating_criteria.reordered');
    }
}
