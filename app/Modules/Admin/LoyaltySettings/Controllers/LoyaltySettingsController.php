<?php

namespace App\Modules\Admin\LoyaltySettings\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Admin\LoyaltySettings\Services\LoyaltySettingsService;
use App\Modules\Admin\LoyaltySettings\Requests\AdminLoyaltySettingsIndexRequest;
use App\Modules\Admin\LoyaltySettings\Requests\CreateLoyaltySettingRequest;
use App\Modules\Admin\LoyaltySettings\Resources\AdminLoyaltySettingResource;

class LoyaltySettingsController extends BaseApiController
{
    protected LoyaltySettingsService $service;

    public function __construct(LoyaltySettingsService $service)
    {
        $this->service = $service;
    }

    public function index(AdminLoyaltySettingsIndexRequest $request)
    {
        $filters = $request->validated();
        $p = $this->service->list($filters);
        return $this->paginated($p, 'admin.loyalty_settings.list');
    }

    public function create(CreateLoyaltySettingRequest $request)
    {
        $data = $request->validated();
        $row = $this->service->create($data);
        return $this->success(new AdminLoyaltySettingResource($row), 'admin.loyalty_settings.created');
    }

    public function activate($id)
    {
        $row = $this->service->activate((int) $id);
        if (! $row) return $this->error('Not found', null, 404);
        return $this->success(new AdminLoyaltySettingResource($row), 'admin.loyalty_settings.activated');
    }
}
