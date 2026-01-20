<?php

namespace App\Modules\Admin\Points\Services;

use App\Models\PointsTransaction;
use Illuminate\Pagination\LengthAwarePaginator;

class PointsAdminService
{
    public function list(array $filters): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 20);
        $query = PointsTransaction::with(['user'])->orderBy('created_at', 'desc');

        if (! empty($filters['user_id'])) $query->where('user_id', (int) $filters['user_id']);
        if (! empty($filters['type'])) $query->where('type', $filters['type']);
        if (! empty($filters['source'])) $query->whereJsonContains('meta->source', $filters['source']);
        if (! empty($filters['date_from'])) $query->whereDate('created_at', '>=', $filters['date_from']);
        if (! empty($filters['date_to'])) $query->whereDate('created_at', '<=', $filters['date_to']);
        if (! empty($filters['min_points'])) $query->where('points', '>=', (int) $filters['min_points']);
        if (! empty($filters['max_points'])) $query->where('points', '<=', (int) $filters['max_points']);
        if (isset($filters['has_expired'])) {
            if ((bool) $filters['has_expired']) $query->whereNotNull('expires_at')->where('expires_at', '<=', now());
            else $query->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); });
        }

        // filter by branch/place if present in meta
        if (! empty($filters['branch_id'])) $query->whereJsonContains('meta->branch_id', (int) $filters['branch_id']);
        if (! empty($filters['place_id'])) $query->whereJsonContains('meta->place_id', (int) $filters['place_id']);

        $page = (int) ($filters['page'] ?? 1);
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function find(int $id)
    {
        return PointsTransaction::with(['user','reference'])->find($id);
    }
}
