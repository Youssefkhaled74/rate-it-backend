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

        $meta = [
            'page' => $p->currentPage(),
            'limit' => $p->perPage(),
            'total' => $p->total(),
            'has_next' => $p->hasMorePages(),
            'last_page' => $p->lastPage(),
            'status_counts' => $statusCounts,
        ];

        return $this->success($p->items(), 'admin.invites.list', $meta);
    }

    public function show($id)
    {
        $invite = $this->service->find((int)$id);
        if (! $invite) return $this->error('admin.invites.not_found', null, 404);
        return $this->success(new AdminInviteResource($invite), 'admin.invites.details');
    }
}
