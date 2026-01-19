<?php

namespace App\Modules\Admin\Catalog\Controllers;

use App\Support\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Modules\Admin\Catalog\Requests\StoreCategoryRequest;
use App\Modules\Admin\Catalog\Requests\UpdateCategoryRequest;
use App\Modules\Admin\Catalog\Resources\CategoryResource;
use App\Modules\Admin\Catalog\Services\CategoryService;

class CategoriesController extends BaseApiController
{
    protected CategoryService $service;

    public function __construct(CategoryService $service)
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
        return $this->success(CategoryResource::collection($items), 'categories.list');
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->only(['name_en','name_ar','is_active','logo']);
        $cat = $this->service->create($data);
        return $this->created(new CategoryResource($cat), 'categories.created');
    }

    public function show($id)
    {
        $cat = $this->service->find((int) $id);
        if (! $cat) {
            return $this->error('Not found', null, 404);
        }
        return $this->success(new CategoryResource($cat));
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $cat = $this->service->update((int) $id, $request->only(['name_en','name_ar','is_active','logo','sort_order']));
        if (! $cat) {
            return $this->error('Not found', null, 404);
        }
        return $this->success(new CategoryResource($cat), 'categories.updated');
    }

    public function destroy($id)
    {
        $ok = $this->service->delete((int) $id);
        if (! $ok) {
            return $this->error('Not found', null, 404);
        }
        return $this->noContent('categories.deleted');
    }
}
