<?php

namespace App\Modules\User\Categories\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Categories\Requests\SubcategoriesIndexRequest;
use App\Modules\User\Categories\Services\CategoryService;
use App\Modules\User\Categories\Resources\SubcategoryResource;

class CategorySubcategoriesController extends BaseApiController
{
    protected CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index(SubcategoriesIndexRequest $request, $category)
    {
        $q = $request->input('q');
        $limit = $request->getLimit();

        $items = $this->service->listSubcategories((int) $category, $q, $limit);

        return $this->success(SubcategoryResource::collection($items), 'categories.subcategories.list');
    }
}
