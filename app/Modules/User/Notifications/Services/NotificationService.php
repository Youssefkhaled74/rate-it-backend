<?php

namespace App\Modules\User\Notifications\Services;

use App\Models\UserNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class NotificationService
{
    public function list($user, int $perPage = 10, bool $unreadOnly = false): LengthAwarePaginator
    {
        $query = UserNotification::query()->where('user_id', $user->id)->orderBy('created_at', 'desc');
        if ($unreadOnly) {
            if (Schema::hasColumn('user_notifications', 'is_read')) {
                $query->where('is_read', false);
            } else {
                $query->whereNull('read_at');
            }
        }

        $p = $query->paginate($perPage);
        return $p;
    }

    public function unreadCount($user): int
    {
        if (Schema::hasColumn('user_notifications', 'is_read')) {
            return UserNotification::where('user_id', $user->id)->where('is_read', false)->count();
        }
        return UserNotification::where('user_id', $user->id)->whereNull('read_at')->count();
    }

    public function getById($user, $id): UserNotification
    {
        $n = UserNotification::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        return $n;
    }

    public function markRead($user, $id): UserNotification
    {
        $n = $this->getById($user, $id);
        if (Schema::hasColumn('user_notifications', 'is_read')) {
            $n->is_read = true;
        }
        $n->read_at = Carbon::now();
        $n->save();
        return $n;
    }

    public function markAllRead($user): void
    {
        $query = UserNotification::where('user_id', $user->id);
        if (Schema::hasColumn('user_notifications', 'is_read')) {
            $query->where('is_read', false)->update(['is_read' => true, 'read_at' => Carbon::now()]);
        } else {
            $query->whereNull('read_at')->update(['read_at' => Carbon::now()]);
        }
    }

    public function delete($user, $id): void
    {
        $n = UserNotification::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $n->delete();
    }

    public function clearAll($user): void
    {
        UserNotification::where('user_id', $user->id)->delete();
    }
}
