<?php

namespace App\Modules\Admin\Notifications\Templates\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TemplatesService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $q = DB::table('notification_templates')->orderBy('created_at','desc');
        if (isset($filters['is_active'])) $q->where('is_active', (bool)$filters['is_active']);
        if (!empty($filters['q'])) {
            $term = '%'.trim($filters['q']).'%';
            $q->where(function($qb) use ($term) {
                $qb->where('type','like',$term)->orWhere('title_tpl','like',$term)->orWhere('title_en','like',$term);
            });
        }
        return $q->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data, $admin)
    {
        $data['created_by_admin_id'] = $admin->id ?? null;
        $id = DB::table('notification_templates')->insertGetId($data);
        return DB::table('notification_templates')->where('id',$id)->first();
    }

    public function update(int $id, array $data, $admin)
    {
        $tpl = DB::table('notification_templates')->where('id',$id);
        if (! $tpl->exists()) return null;
        $data['updated_by_admin_id'] = $admin->id ?? null;
        $tpl->update($data);
        return DB::table('notification_templates')->where('id',$id)->first();
    }
}
