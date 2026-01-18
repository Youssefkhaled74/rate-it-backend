<?php

namespace App\Modules\User\Lookups\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Lookups\Services\LookupsService;
use Illuminate\Http\Request;

class GendersController extends BaseApiController
{
    protected $service;

    public function __construct(LookupsService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $genders = $this->service->getGenders();

        return $this->success($genders, 'lookups.genders');
    }
}
