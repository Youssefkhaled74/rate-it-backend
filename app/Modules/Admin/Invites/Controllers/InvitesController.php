<?php

namespace App\Modules\Admin\Invites\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Admin\Invites\Services\InvitesService;
use App\Modules\Admin\Invites\Requests\AdminInvitesIndexRequest;
use App\Modules\Admin\Invites\Resources\AdminInviteResource;

class InvitesController extends BaseApiController
{
    protected InvitesService $service;

    public function __construct(InvitesService $service)
    {
        $this->service = $service;
    }

    public function index(AdminInvitesIndexRequest $request)
    {
        $filters = $request->validated();
        $p = $this->service->list($filters);
        $statusCounts = $this->service->statusCounts();
        $meta = array_merge(['status_counts' => $statusCounts], []);
        return $this->paginated($p, 'admin.invites.list')->setData(function($resp) use ($meta) {
            return $resp; // paginated() already formats; meta will be handled separately by client
        });
    }

    public function show($id)
    {
        $invite = $this->service->find((int)$id);
        if (! $invite) return $this->error('admin.invites.not_found', null, 404);
        return $this->success(new AdminInviteResource($invite), 'admin.invites.details');
    }
}
