<?php

namespace App\Modules\Admin\Notifications\Broadcast\Controllers;

use App\Support\Api\BaseApiController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Modules\Admin\Notifications\Broadcast\Requests\SendBroadcastRequest;
use App\Modules\Admin\Notifications\Broadcast\Services\BroadcastService;

class BroadcastController extends BaseApiController
{
    protected BroadcastService $service;

    public function __construct(BroadcastService $service)
    {
        $this->service = $service;
    }

    public function send(SendBroadcastRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user() ?? new User();
        $result = $this->service->broadcast($data, $user);
        return $this->success($result, 'admin.notifications.broadcast.sent');
    }
}
