<?php

namespace App\Modules\Admin\Catalog\Controllers;

use App\Support\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Models\Subcategory;
use App\Models\Category;
use App\Modules\Admin\Catalog\Requests\StoreSubcategoryRequest;
use App\Modules\Admin\Catalog\Requests\UpdateSubcategoryRequest;
use App\Modules\Admin\Catalog\Resources\SubcategoryResource;
use App\Modules\Admin\Catalog\Services\SubcategoryService;

class SubcategoriesController extends BaseApiController
{
    protected SubcategoryService $service;

    public function __construct(SubcategoryService $service)
    {
        $this->service = $service;
    }
    public function index(Request $request)
    {
        $filters = [];
        if ($request->has('category_id')) {
            $filters['category_id'] = (int) $request->query('category_id');
        }
        if ($request->has('active')) {
            $filters['active'] = $request->query('active');
        }
        $items = $this->service->list($filters);
        return $this->success(SubcategoryResource::collection($items));
    }

    public function store(StoreSubcategoryRequest $request)
    {
        $cat = Category::find($request->input('category_id'));
        if (! $cat) {
            return $this->error('Category not found', null, 404);
        }
        $data = $request->only(['category_id','name_en','name_ar','image','is_active']);
        $sub = $this->service->create($data);
        return $this->created(new SubcategoryResource($sub));
    }

    public function show($id)
    {
        $sub = $this->service->find((int) $id);
        if (! $sub) return $this->error('Not found', null, 404);
        return $this->success(new SubcategoryResource($sub));
    }

    public function update(UpdateSubcategoryRequest $request, $id)
    {
        $sub = $this->service->update((int) $id, $request->only(['name_en','name_ar','image','is_active','category_id','sort_order']));
        if (! $sub) return $this->error('Not found', null, 404);
        return $this->success(new SubcategoryResource($sub));
    }

    public function destroy($id)
    {
        try {
            $ok = $this->service->delete((int) $id);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, 409);
        }
        if (! $ok) return $this->error('Not found', null, 404);
        return $this->noContent('subcategory.deleted');
    }
}
