<?php

namespace App\Modules\User\Categories\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Categories\Requests\CategoriesSearchRequest;
use App\Modules\User\Categories\Services\CategoryService;
use App\Modules\User\Categories\Resources\CategorySearchItemResource;

class CategoriesSearchController extends BaseApiController
{
    protected CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index(CategoriesSearchRequest $request)
    {
        $q = $request->input('q');
        $limit = $request->getLimit();
        $categoryId = $request->input('category_id');
        $types = $request->getTypes();

        $payload = $this->service->search($q, $categoryId, $types, $limit);

        // wrap results in resources
        $payload['results'] = array_map(function ($item) {
            return (new CategorySearchItemResource($item))->toArray(request());
        }, $payload['results']);

        return $this->success($payload, 'categories.search.results');
    }
}
