<?php

namespace App\Modules\User\Brands\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Brands\Services\BrandService;
use App\Modules\User\Brands\Resources\BrandDetailsResource;
use App\Modules\User\Brands\Resources\PlaceCardResource;

class BrandController extends BaseApiController
{
    protected BrandService $service;

    public function __construct(BrandService $service)
    {
        $this->service = $service;
    }

    public function show($brand)
    {
        $data = $this->service->getBrandDetails((int) $brand);
        return $this->success(new BrandDetailsResource($data), 'brands.details');
    }

    public function places($brand)
    {
        $items = $this->service->listBrandPlaces((int) $brand);
        return $this->success(PlaceCardResource::collection($items), 'brands.places');
    }
}
