<?php

namespace App\Modules\Admin\Subscriptions\Subscriptions\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Admin\Subscriptions\Subscriptions\Services\SubscriptionsService;
use App\Modules\Admin\Subscriptions\Subscriptions\Requests\AdminSubscriptionsIndexRequest;
use App\Modules\Admin\Subscriptions\Subscriptions\Resources\AdminSubscriptionResource;

class SubscriptionsController extends BaseApiController
{
    protected SubscriptionsService $service;

    public function __construct(SubscriptionsService $service)
    {
        $this->service = $service;
    }

    public function index(AdminSubscriptionsIndexRequest $request)
    {
        $p = $this->service->list($request->validated());
        return $this->paginated($p, 'admin.subscriptions.list');
    }
}
