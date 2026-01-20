<?php

namespace App\Modules\Admin\Invites\Services;

use App\Models\Invite;
use Illuminate\Support\Facades\DB;

class InvitesService
{
    public function list(array $filters = [])
    {
        $q = Invite::with(['inviter','invitedUser'])->orderBy($filters['sort'] ?? 'created_at', $filters['direction'] ?? 'desc');

        if (!empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }

        if (!empty($filters['q'])) {
            $term = '%' . trim($filters['q']) . '%';
            $q->where(function($qb) use ($term) {
                $qb->where('invited_phone','like',$term)
                   ->orWhereHas('inviter', function($qi) use ($term) { $qi->where('name','like',$term)->orWhere('phone','like',$term); })
                   ->orWhere('id','like',trim($term,'%'));
            });
        }

        if (!empty($filters['inviter_id'])) {
            $q->where('inviter_user_id', (int)$filters['inviter_id']);
        }

        if (!empty($filters['invitee_user_id'])) {
            $q->where('invited_user_id', (int)$filters['invitee_user_id']);
        }

        if (!empty($filters['from'])) {
            $q->whereDate('created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $q->whereDate('created_at', '<=', $filters['to']);
        }

        $per = $filters['per_page'] ?? 15;
        return $q->paginate($per);
    }

    public function find(int $id)
    {
        return Invite::with(['inviter','invitedUser'])->find($id);
    }

    public function statusCounts(): array
    {
        $rows = DB::table('invites')->select('status', DB::raw('count(*) as cnt'))->groupBy('status')->get();
        $out = [];
        foreach ($rows as $r) $out[$r->status] = (int)$r->cnt;
        return $out;
    }
}
