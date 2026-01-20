<?php

namespace App\Modules\Admin\Points\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Admin\Points\Services\PointsAdminService;
use App\Modules\Admin\Points\Requests\AdminPointsTransactionsIndexRequest;
use App\Modules\Admin\Points\Resources\AdminPointsTransactionResource;

class PointsController extends BaseApiController
{
    protected PointsAdminService $service;

    public function __construct(PointsAdminService $service)
    {
        $this->service = $service;
    }

    public function index(AdminPointsTransactionsIndexRequest $request)
    {
        $filters = $request->validated();
        $p = $this->service->list($filters);
        return $this->paginated($p, 'admin.points.transactions.list');
    }

    public function show($id)
    {
        $t = $this->service->find((int) $id);
        if (! $t) return $this->error('Not found', null, 404);
        return $this->success(new AdminPointsTransactionResource($t), 'admin.points.transactions.detail');
    }
}
