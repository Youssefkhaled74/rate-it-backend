<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Review;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class UsersService
{
    public function listUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $select = ['id','name','full_name','email','phone','gender_id','nationality_id','created_at'];

        // include avatar-like columns if present
        $avatarCandidates = ['avatar_path','avatar','photo_path','picture'];
        foreach ($avatarCandidates as $col) {
            if (Schema::hasColumn('users', $col)) $select[] = $col;
        }

        $query = User::query()->select(array_unique($select))->with(['gender','nationality']);

        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function($s) use ($q) {
                $s->where('name', 'like', "%{$q}%")
                  ->orWhere('full_name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        // include reviews count
        $query->withCount('reviews');

        return $query->orderBy('created_at','desc')->paginate($perPage);
    }

    /**
     * Return profile data for a user: profile, stats and recent reviews
     * @return array
     */
    public function getUserProfile(User $user): array
    {
        $uid = $user->id;

        $total = Review::where('user_id', $uid)->count();
        $good = Review::where('user_id', $uid)->where('overall_rating', '>=', 4)->count();
        $bad  = Review::where('user_id', $uid)->where('overall_rating', '<=', 2)->count();

        $recent = Review::where('user_id', $uid)
            ->with(['place'])
            ->orderBy('created_at','desc')
            ->limit(10)
            ->get()
            ->map(function($r){
                $rating = (float) ($r->overall_rating ?? 0);
                $emoji = $this->ratingEmoji($rating);
                $placeName = '-';
                if ($r->place) {
                    $placeName = $r->place->getDisplayNameAttribute();
                }
                return [
                    'id' => $r->id,
                    'place_name' => $placeName,
                    'created_at' => $r->created_at,
                    'comment' => $r->comment ?? '',
                    'rating' => $rating,
                    'emoji' => $emoji,
                ];
            });

        // attempt wallet and points fields if present
        $wallet = null;
        if (Schema::hasColumn('users', 'wallet_balance')) $wallet = $user->wallet_balance;
        if (empty($wallet) && array_key_exists('wallet_balance', $user->getAttributes())) $wallet = $user->wallet_balance ?? null;

        $points = null;
        if (Schema::hasColumn('users', 'points')) $points = $user->points;
        if (empty($points) && array_key_exists('points', $user->getAttributes())) $points = $user->points ?? null;

        // avatar resolution: look for common columns
        $avatar = null;
        foreach (['avatar_path','avatar','photo_path','picture'] as $col) {
            if (array_key_exists($col, $user->getAttributes()) && ! empty($user->{$col})) {
                $path = $user->{$col};
                // if public file exists, use asset, otherwise storage proxy
                if (file_exists(public_path($path))) {
                    $avatar = asset($path);
                } elseif (Storage::disk('public')->exists($path)) {
                    $publicFile = public_path($path);
                    if (file_exists($publicFile)) $avatar = asset($path);
                    else $avatar = route('storage.proxy', ['path' => $path]);
                }
                break;
            }
        }

        return [
            'user' => $user,
            'stats' => [
                'total' => $total,
                'good' => $good,
                'bad' => $bad,
                'wallet' => $wallet,
                'points' => $points,
            ],
            'recent' => $recent,
            'avatar' => $avatar,
        ];
    }

    protected function ratingEmoji(float $rating): string
    {
        if ($rating >= 4.5) return '😍';
        if ($rating >= 4) return '😊';
        if ($rating >= 3) return '😐';
        if ($rating >= 2) return '😕';
        return '😡';
    }
}
