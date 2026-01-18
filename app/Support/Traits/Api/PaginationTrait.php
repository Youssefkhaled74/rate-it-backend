<?php

namespace App\Support\Traits\Api;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginationTrait
{
    public function parsePagination(Request $request, int $defaultLimit = 20, int $maxLimit = 100): array
    {
        $page = (int) max(1, $request->query('page', 1));
        $limit = (int) min($maxLimit, max(1, $request->query('limit', $defaultLimit)));
        $sort = $request->query('sort', 'id');
        $order = strtolower($request->query('order', 'desc')) === 'asc' ? 'asc' : 'desc';

        return [$page, $limit, $sort, $order];
    }

    public function buildPaginationMeta(LengthAwarePaginator $p): array
    {
        return [
            'page' => $p->currentPage(),
            'limit' => $p->perPage(),
            'total' => $p->total(),
            'has_next' => $p->hasMorePages(),
            'last_page' => $p->lastPage(),
        ];
    }
}
