<?php

namespace App\Modules\Admin\Catalog\Controllers;

use App\Support\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Modules\Admin\Catalog\Requests\StoreBrandRequest;
use App\Modules\Admin\Catalog\Requests\UpdateBrandRequest;
use App\Modules\Admin\Catalog\Resources\BrandResource;
use App\Modules\Admin\Catalog\Services\BrandService;

class BrandsController extends BaseApiController
{
    protected BrandService $service;

    public function __construct(BrandService $service)
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
        return $this->success(BrandResource::collection($items), 'brands.list');
    }

    public function store(StoreBrandRequest $request)
    {
        $data = $request->only(['name_en','name_ar','logo','is_active']);
        $brand = $this->service->create($data);
        return $this->created(new BrandResource($brand), 'brands.created');
    }

    public function show($id)
    {
        $brand = $this->service->find((int) $id);
        if (! $brand) {
            return $this->error('Not found', null, 404);
        }
        return $this->success(new BrandResource($brand));
    }

    public function update(UpdateBrandRequest $request, $id)
    {
        $brand = $this->service->update((int) $id, $request->only(['name_en','name_ar','logo','is_active','sort_order']));
        if (! $brand) {
            return $this->error('Not found', null, 404);
        }
        return $this->success(new BrandResource($brand), 'brands.updated');
    }

    public function destroy($id)
    {
        $ok = $this->service->delete((int) $id);
        if (! $ok) {
            return $this->error('Not found', null, 404);
        }
        return $this->noContent('brands.deleted');
    }
}
