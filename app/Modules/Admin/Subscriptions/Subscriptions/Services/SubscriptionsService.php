<?php

namespace App\Modules\Admin\Subscriptions\Subscriptions\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class SubscriptionsService
{
    public function list(array $filters = [])
    {
        $q = Subscription::with(['plan','user'])->orderBy($filters['sort'] ?? 'created_at', $filters['direction'] ?? 'desc');

        if (!empty($filters['status'])) {
            $q->where('subscription_status', $filters['status']);
        }
        if (!empty($filters['user_id'])) {
            $q->where('user_id', (int)$filters['user_id']);
        }
        if (!empty($filters['plan_id'])) {
            $q->where('subscription_plan_id', (int)$filters['plan_id']);
        }
        if (!empty($filters['from'])) $q->whereDate('created_at','>=',$filters['from']);
        if (!empty($filters['to'])) $q->whereDate('created_at','<=',$filters['to']);
        if (!empty($filters['q'])) {
            $t = '%'.trim($filters['q']).'%';
            $q->whereHas('user', function($qu) use ($t){ $qu->where('name','like',$t)->orWhere('phone','like',$t); });
        }

        return $q->paginate($filters['per_page'] ?? 15);
    }
}
