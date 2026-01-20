<?php

namespace App\Modules\Admin\Users\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Admin\Users\Services\UserAdminService;
use App\Modules\Admin\Users\Requests\AdminUsersIndexRequest;
use App\Modules\Admin\Users\Requests\BlockUserRequest;
use App\Modules\Admin\Users\Resources\AdminUserResource;
use App\Modules\Admin\Users\Resources\AdminUserListResource;

class UsersController extends BaseApiController
{
    protected UserAdminService $service;

    public function __construct(UserAdminService $service)
    {
        $this->service = $service;
    }

    public function index(AdminUsersIndexRequest $request)
    {
        $filters = $request->validated();
        $p = $this->service->list($filters);
        return $this->paginated($p, 'admin.users.list');
    }

    public function show($id)
    {
        $u = $this->service->find((int) $id);
        if (! $u) return $this->error('Not found', null, 404);
        return $this->success(new AdminUserResource($u), 'admin.users.details');
    }

    public function block(BlockUserRequest $request, $id)
    {
        $data = $request->validated();
        $u = $this->service->block((int) $id, $data);
        if (! $u) return $this->error('Not found', null, 404);
        return $this->success(new AdminUserResource($u), $data['is_blocked'] ? 'admin.users.blocked' : 'admin.users.unblocked');
    }

    public function reviews($id)
    {
        $p = $this->service->reviews((int) $id, request()->all());
        return $this->paginated($p, 'admin.users.reviews');
    }

    public function points($id)
    {
        $payload = $this->service->points((int) $id, request()->all());
        return $this->success($payload, 'admin.users.points');
    }
}
