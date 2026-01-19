<?php

namespace App\Modules\User\Invites\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Invites\Requests\CheckPhonesRequest;
use App\Modules\User\Invites\Requests\CreateInvitesRequest;
use App\Modules\User\Invites\Services\InviteService;
use App\Modules\User\Invites\Resources\InviteResource;
use Illuminate\Http\Request;

class InvitesController extends BaseApiController
{
    protected InviteService $service;

    public function __construct(InviteService $service)
    {
        $this->service = $service;
    }

    public function checkPhones(CheckPhonesRequest $request)
    {
        $phones = $request->input('phones', []);
        $result = $this->service->checkPhones($phones);
        return $this->success($result, 'invites.check_success');
    }

    public function store(CreateInvitesRequest $request)
    {
        $user = $request->user();
        $phones = $request->input('phones', []);

        $result = $this->service->createInvites($user, $phones);
        return $this->success($result, 'invites.created');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $list = $this->service->listInvites($user);
        return $this->success(InviteResource::collection($list), 'invites.list');
    }
}
