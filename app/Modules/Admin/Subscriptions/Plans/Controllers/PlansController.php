<?php

namespace App\Modules\Admin\Subscriptions\Plans\Controllers;

use App\Support\Api\BaseApiController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user() ?? new User();
        $row = $this->service->create($request->validated(), $user);
        return $this->success(new AdminSubscriptionPlanResource($row), 'admin.subscriptions.plans.created');
    }

    public function update(UpdatePlanRequest $request, $id)
    {
        $user = Auth::user() ?? new User();
        $row = $this->service->update((int)$id, $request->validated(), $user);
        if (! $row) return $this->error('Not found', null, 404);
        return $this->success(new AdminSubscriptionPlanResource($row), 'admin.subscriptions.plans.updated');
    }

    public function activate($id)
    {
        $user = Auth::user() ?? new User();
        $row = $this->service->activate((int)$id, $user ?? new User());
        if (! $row) return $this->error('Not found', null, 404);
        return $this->success(new AdminSubscriptionPlanResource($row), 'admin.subscriptions.plans.activated');
    }
}
