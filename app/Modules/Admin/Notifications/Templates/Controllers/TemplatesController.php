<?php

namespace App\Modules\Admin\Notifications\Templates\Controllers;

use App\Support\Api\BaseApiController;
use App\Models\User;
use App\Modules\Admin\Notifications\Templates\Services\TemplatesService;
use App\Modules\Admin\Notifications\Templates\Requests\AdminTemplatesIndexRequest;
use App\Modules\Admin\Notifications\Templates\Requests\StoreNotificationTemplateRequest;
use App\Modules\Admin\Notifications\Templates\Requests\UpdateNotificationTemplateRequest;
use App\Modules\Admin\Notifications\Templates\Resources\AdminNotificationTemplateResource;

class TemplatesController extends BaseApiController
{
    protected TemplatesService $service;

    public function __construct(TemplatesService $service)
    {
        $this->service = $service;
    }

    public function index(AdminTemplatesIndexRequest $request)
    {
        $p = $this->service->list($request->validated());
        return $this->paginated($p, 'admin.notifications.templates.list');
    }

    public function store(StoreNotificationTemplateRequest $request)
    {
        $data = $request->validated();
        $user = (auth()?->user()) ?: new User();
        $t = $this->service->create($data, $user);
        return $this->success(new AdminNotificationTemplateResource($t), 'admin.notifications.templates.created');
    }

    public function update(UpdateNotificationTemplateRequest $request, $id)
    {
        $data = $request->validated();
        $user = (auth()?->user()) ?: new User();
        $t = $this->service->update((int)$id, $data, $user ?? new User());
        if (! $t) return $this->error('Not found', null, 404);
        return $this->success(new AdminNotificationTemplateResource($t), 'admin.notifications.templates.updated');
    }
}
