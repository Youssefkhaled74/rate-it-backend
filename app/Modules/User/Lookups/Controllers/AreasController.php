<?php

namespace App\Modules\User\Lookups\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Lookups\Services\LookupsService;
use Illuminate\Http\Request;

class AreasController extends BaseApiController
{
    protected $service;

    public function __construct(LookupsService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $cityId = $request->integer('city_id') ?: null;
        $items = $this->service->getAreas($cityId);

        return $this->success($items, 'lookups.areas');
    }
}
