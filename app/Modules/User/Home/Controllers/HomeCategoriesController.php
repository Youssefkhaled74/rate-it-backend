<?php

namespace App\Modules\User\Home\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Home\Services\HomeCategoriesService;
use App\Modules\User\Home\Resources\CategoryResource;

class HomeCategoriesController extends BaseApiController
{
    protected HomeCategoriesService $service;

    public function __construct(HomeCategoriesService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->list();
        return $this->success(CategoryResource::collection($items), 'home.categories.list');
    }
}
