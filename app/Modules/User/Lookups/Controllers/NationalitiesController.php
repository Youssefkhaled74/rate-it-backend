<?php

namespace App\Modules\User\Lookups\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Lookups\Services\LookupsService;
use App\Modules\User\Lookups\Resources\NationalityResource;
use Illuminate\Http\Request;

class NationalitiesController extends BaseApiController
{
    protected $service;

    public function __construct(LookupsService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $items = $this->service->getNationalities();

        return $this->success($items, 'lookups.nationalities');
    }
}
