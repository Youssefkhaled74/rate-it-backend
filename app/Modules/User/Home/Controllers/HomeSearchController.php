<?php

namespace App\Modules\User\Home\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Home\Requests\HomeSearchRequest;
use App\Modules\User\Home\Services\HomeSearchService;
use App\Modules\User\Home\Resources\HomeSearchItemResource;

class HomeSearchController extends BaseApiController
{
    protected HomeSearchService $service;

    public function __construct(HomeSearchService $service)
    {
        $this->service = $service;
    }

    public function index(HomeSearchRequest $request)
    {
        $data = $request->validated();
        $q = $data['q'];
        $limit = (int) ($data['limit'] ?? 10);
        $types = null;
        if (!empty($data['types'])) {
            $types = array_filter(array_map('trim', explode(',', $data['types'])));
        }

        $results = $this->service->search($q, $limit, $types);

        $payload = [
            'query' => $q,
            'limit' => $limit,
            'results' => HomeSearchItemResource::collection($results),
        ];

        return $this->success($payload, 'home.search.results');
    }
}
