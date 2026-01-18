<?php

namespace App\Modules\User\Brands\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Brands\Services\PlaceService;
use App\Modules\User\Brands\Resources\PlaceDetailsResource;

class PlaceController extends BaseApiController
{
    protected PlaceService $service;

    public function __construct(PlaceService $service)
    {
        $this->service = $service;
    }

    public function show($place)
    {
        $data = $this->service->getPlaceDetails((int) $place);
        return $this->success(new PlaceDetailsResource($data), 'places.details');
    }

    public function reviews($place)
    {
        $paginator = $this->service->listPlaceReviews((int) $place, 10);
        return $this->paginated($paginator, 'places.reviews');
    }
}
