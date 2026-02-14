<?php

namespace App\Modules\User\Home\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Home\Requests\HomePageRequest;
use App\Modules\User\Home\Services\HomePageService;

class HomePageController extends BaseApiController
{
    public function __construct(protected HomePageService $service)
    {
    }

    public function index(HomePageRequest $request)
    {
        $payload = $this->service->getPageData($request->getPerPage());

        return $this->success($payload, 'home.page.show');
    }
}
