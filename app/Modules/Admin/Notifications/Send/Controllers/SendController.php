<?php

namespace App\Modules\Admin\Notifications\Send\Controllers;

use App\Support\Api\BaseApiController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user() ?? new User();
        $n = $this->service->sendToUser((int)$id, $data, $user);
        if (! $n) return $this->error('Not found', null, 404);
        return $this->success($n, 'admin.notifications.user.sent');
    }
}
