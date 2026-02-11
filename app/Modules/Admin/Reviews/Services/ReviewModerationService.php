<?php

namespace App\Modules\Admin\Reviews\Services;

use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ReviewModerationService
{
    public function list(array $filters): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 15);
        $query = Review::query()->with(['user:id,name,phone','place:id,name,logo','branch:id,name,name_en,name_ar'])
            ->withCount(['answers','photos']);

        if (! empty($filters['place_id'])) $query->where('place_id', (int) $filters['place_id']);
        if (! empty($filters['branch_id'])) $query->where('branch_id', (int) $filters['branch_id']);
        if (! empty($filters['user_id'])) $query->where('user_id', (int) $filters['user_id']);

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['rating_min'])) $query->where('overall_rating', '>=', (float) $filters['rating_min']);
        if (isset($filters['rating_max'])) $query->where('overall_rating', '<=', (float) $filters['rating_max']);

        if (isset($filters['is_hidden'])) $query->where('is_hidden', (bool) $filters['is_hidden']);
        if (isset($filters['is_featured'])) $query->where('is_featured', (bool) $filters['is_featured']);

        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($q2) use ($q) {
                $q2->where('comment', 'like', "%{$q}%")
                    ->orWhereHas('user', fn($s) => $s->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%"))
                    ->orWhereHas('place', fn($s) => $s->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('branch', fn($s) => $s->where('name', 'like', "%{$q}%"));
            });
        }

        $query->orderBy('created_at', 'desc');

        $page = (int) ($filters['page'] ?? 1);
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function find(int $id)
    {
        return Review::with(['user','place','branch','answers.criteria','answers.choice','photos'])->find($id);
    }

    public function hide(int $id, array $data)
    {
        $review = Review::find($id);
        if (! $review) return null;

        $review->is_hidden = (bool) $data['is_hidden'];
        $review->hidden_reason = $data['reason'] ?? null;
        $review->hidden_at = $review->is_hidden ? Carbon::now() : null;
        // attach admin id when present in request attributes
        $admin = request()->get('admin');
        $review->hidden_by_admin_id = $review->is_hidden && $admin ? $admin->id : null;
        $review->save();
        return $this->find($id);
    }

    public function reply(int $id, array $data)
    {
        $review = Review::find($id);
        if (! $review) return null;
        $review->admin_reply_text = $data['reply_text'];
        $review->replied_at = Carbon::now();
        $admin = request()->get('admin');
        $review->replied_by_admin_id = $admin ? $admin->id : null;
        $review->save();
        return $this->find($id);
    }

    public function feature(int $id, array $data)
    {
        $review = Review::find($id);
        if (! $review) return null;
        $review->is_featured = (bool) $data['is_featured'];
        $review->featured_at = $review->is_featured ? Carbon::now() : null;
        $admin = request()->get('admin');
        $review->featured_by_admin_id = $review->is_featured && $admin ? $admin->id : null;
        $review->save();
        return $this->find($id);
    }
}
