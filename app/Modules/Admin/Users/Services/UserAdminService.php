<?php

namespace App\Modules\Admin\Users\Services;

use App\Models\User;
use App\Models\Review;
use App\Models\PointsTransaction;
use App\Modules\User\Points\Services\PointsService;
use Carbon\Carbon;

class UserAdminService
{
    public function list(array $filters)
    {
        $perPage = (int) ($filters['per_page'] ?? 15);
        $query = User::query();

        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($q2) use ($q) {
                $q2->where('full_name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if (! empty($filters['phone'])) $query->where('phone', $filters['phone']);
        if (! empty($filters['email'])) $query->where('email', $filters['email']);

        if (isset($filters['is_phone_verified'])) $query->whereNotNull('phone_verified_at', (bool) $filters['is_phone_verified']);
        if (isset($filters['is_blocked'])) $query->where('is_blocked', (bool) $filters['is_blocked']);

        if (! empty($filters['gender_id'])) $query->where('gender_id', (int) $filters['gender_id']);
        if (! empty($filters['nationality_id'])) $query->where('nationality_id', (int) $filters['nationality_id']);

        if (! empty($filters['created_from'])) $query->whereDate('created_at', '>=', $filters['created_from']);
        if (! empty($filters['created_to'])) $query->whereDate('created_at', '<=', $filters['created_to']);

        $query->orderBy('created_at', 'desc');

        $page = (int) ($filters['page'] ?? 1);
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function find(int $id)
    {
        return User::with(['gender','nationality'])->find($id);
    }

    public function block(int $id, array $data)
    {
        $u = User::find($id);
        if (! $u) return null;
        $u->is_blocked = (bool) $data['is_blocked'];
        $u->blocked_reason = $data['reason'] ?? null;
        $u->blocked_at = $u->is_blocked ? Carbon::now() : null;
        $admin = request()->get('admin');
        $u->blocked_by_admin_id = $u->is_blocked && $admin ? $admin->id : null;
        $u->save();
        return $this->find($id);
    }

    public function reviews(int $userId, array $filters)
    {
        $perPage = (int) ($filters['per_page'] ?? 15);
        $query = Review::where('user_id', $userId)->with(['place','branch'])->orderBy('created_at','desc');

        if (! empty($filters['place_id'])) $query->where('place_id', (int) $filters['place_id']);
        if (! empty($filters['branch_id'])) $query->where('branch_id', (int) $filters['branch_id']);
        if (! empty($filters['date_from'])) $query->whereDate('created_at', '>=', $filters['date_from']);
        if (! empty($filters['date_to'])) $query->whereDate('created_at', '<=', $filters['date_to']);
        if (isset($filters['rating_min'])) $query->where('overall_rating', '>=', (float) $filters['rating_min']);
        if (isset($filters['rating_max'])) $query->where('overall_rating', '<=', (float) $filters['rating_max']);
        if (isset($filters['is_hidden'])) $query->where('is_hidden', (bool) $filters['is_hidden']);

        $page = (int) ($filters['page'] ?? 1);
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function points(int $userId, array $filters)
    {
        $user = User::find($userId);
        if (! $user) return null;
        $pointsSvc = app(PointsService::class);
        $balance = $pointsSvc->getBalance($user);
        $history = $pointsSvc->getHistory($user, (int) ($filters['per_page'] ?? 20));
        $earned = PointsService::class; // placeholder — compute via transactions
        // compute totals
        $totalEarned = (int) PointsTransaction::where('user_id', $userId)->where('points', '>', 0)->sum('points');
        $totalSpent = (int) PointsTransaction::where('user_id', $userId)->where('points', '<', 0)->sum('points');
        $totalSpent = abs($totalSpent);

        return [
            'points_balance' => $balance,
            'points_earned_total' => $totalEarned,
            'points_spent_total' => $totalSpent,
            'transactions' => $history,
        ];
    }
}
