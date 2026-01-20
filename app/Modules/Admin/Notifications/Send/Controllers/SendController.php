<?php

namespace App\Modules\Admin\Notifications\Send\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Admin\Notifications\Send\Requests\SendToUserRequest;
use App\Modules\Admin\Notifications\Send\Services\SendService;

class SendController extends BaseApiController
{
    protected SendService $service;

    public function __construct(SendService $service)
    {
        $this->service = $service;
    }

    public function sendToUser(SendToUserRequest $request, $id)
    {
        $data = $request->validated();
        $n = $this->service->sendToUser((int)$id, $data, auth()->user());
        if (! $n) return $this->error('Not found', null, 404);
        return $this->success($n, 'admin.notifications.user.sent');
    }
}
