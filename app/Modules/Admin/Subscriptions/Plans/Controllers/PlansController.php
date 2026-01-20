<?php

namespace App\Modules\Admin\Subscriptions\Plans\Controllers;

use App\Support\Api\BaseApiController;
use App\Models\User;
use App\Modules\Admin\Subscriptions\Plans\Services\PlansService;
use App\Modules\Admin\Subscriptions\Plans\Requests\StorePlanRequest;
use App\Modules\Admin\Subscriptions\Plans\Requests\UpdatePlanRequest;
use App\Modules\Admin\Subscriptions\Plans\Requests\PlansIndexRequest;
use App\Modules\Admin\Subscriptions\Plans\Resources\AdminSubscriptionPlanResource;

class PlansController extends BaseApiController
{
    protected PlansService $service;

    public function __construct(PlansService $service)
    {
        $this->service = $service;
    }

    public function index(PlansIndexRequest $request)
    {
        $p = $this->service->list($request->validated());
        return $this->paginated($p, 'admin.subscriptions.plans.list');
    }

    public function store(StorePlanRequest $request)
    {
        $row = $this->service->create($request->validated(), auth()->user() ?? new User());
        return $this->success(new AdminSubscriptionPlanResource($row), 'admin.subscriptions.plans.created');
    }

    public function update(UpdatePlanRequest $request, $id)
    {
        $row = $this->service->update((int)$id, $request->validated(), auth()->user() ?? new User());
        if (! $row) return $this->error('Not found', null, 404);
        return $this->success(new AdminSubscriptionPlanResource($row), 'admin.subscriptions.plans.updated');
    }

    public function activate($id)
    {
        $row = $this->service->activate((int)$id, auth()->user() ?? new User());
        if (! $row) return $this->error('Not found', null, 404);
        return $this->success(new AdminSubscriptionPlanResource($row), 'admin.subscriptions.plans.activated');
    }
}
