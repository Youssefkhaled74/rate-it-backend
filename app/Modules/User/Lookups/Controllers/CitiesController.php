<?php

namespace App\Modules\User\Lookups\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Lookups\Services\LookupsService;
use Illuminate\Http\Request;

class CitiesController extends BaseApiController
{
    protected $service;

    public function __construct(LookupsService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $items = $this->service->getCities();

        return $this->success($items, 'lookups.cities');
    }
}
