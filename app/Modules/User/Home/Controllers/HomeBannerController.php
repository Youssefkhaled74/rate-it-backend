<?php

namespace App\Modules\User\Home\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Home\Services\HomeBannerService;
use App\Modules\User\Home\Resources\HomeBannerResource;
use Illuminate\Http\Request;

class HomeBannerController extends BaseApiController
{
    protected HomeBannerService $service;

    public function __construct(HomeBannerService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $banners = $this->service->listForHome();

        return $this->success(HomeBannerResource::collection($banners), __('home.banners.list'));
    }
}
