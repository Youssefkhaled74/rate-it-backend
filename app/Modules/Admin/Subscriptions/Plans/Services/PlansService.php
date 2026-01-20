<?php

namespace App\Modules\Admin\Subscriptions\Plans\Services;

use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;

class PlansService
{
    public function list(array $filters = [])
    {
        $q = SubscriptionPlan::query()->orderBy($filters['sort'] ?? 'created_at', $filters['direction'] ?? 'desc');
        if (isset($filters['is_active'])) $q->where('is_active', (bool)$filters['is_active']);
        if (!empty($filters['q'])) {
            $term = '%'.trim($filters['q']).'%';
            $q->where(function($qb) use ($term) { $qb->where('name_en','like',$term)->orWhere('name_ar','like',$term)->orWhere('code','like',$term); });
        }
        return $q->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data, $admin)
    {
        $data['created_by_admin_id'] = $admin->id ?? null;
        $plan = SubscriptionPlan::create($data);
        return $plan;
    }

    public function update(int $id, array $data, $admin)
    {
        $plan = SubscriptionPlan::find($id);
        if (! $plan) return null;
        $data['updated_by_admin_id'] = $admin->id ?? null;
        $plan->update($data);
        return $plan->fresh();
    }

    public function activate(int $id, $admin)
    {
        $plan = SubscriptionPlan::find($id);
        if (! $plan) return null;
        $plan->is_active = true;
        $plan->save();
        return $plan->fresh();
    }
}
