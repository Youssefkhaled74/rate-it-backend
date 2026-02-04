<?php

namespace App\Services\Admin;

use App\Models\Review;
use App\Models\User;
use App\Models\Brand;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AdminDashboardService
{
    // cache durations in seconds
    protected int $cacheTtl = 60;

    public function getStats(): array
    {
        return Cache::remember('admin_dashboard_stats', $this->cacheTtl, function () {
            $now = Carbon::now();

            // Average rating last 30 days and previous 30 days
            $periodEnd = $now;
            $periodStart = $now->copy()->subDays(30);
            $prevStart = $now->copy()->subDays(60);
            $prevEnd = $now->copy()->subDays(30);

            $avg = Review::whereBetween('created_at', [$periodStart, $periodEnd])->avg('overall_rating') ?: 0;
            $prevAvg = Review::whereBetween('created_at', [$prevStart, $prevEnd])->avg('overall_rating') ?: 0;

            $avgRounded = round($avg, 1);
            $avgDelta = $prevAvg > 0 ? round((($avg - $prevAvg) / $prevAvg) * 100, 1) : null;

            // Total reviews and percent change vs previous 30 days
            $total = Review::count();
            $totalPrev = Review::whereBetween('created_at', [$prevStart, $prevEnd])->count();
            $totalChange = $totalPrev > 0 ? round((($total - $totalPrev) / max(1, $totalPrev)) * 100, 1) : null;

            // New in last 7 days
            $new7 = Review::where('created_at', '>=', $now->copy()->subDays(7))->count();

            // Pending reply: reviews without admin_reply_text AND without replied_at
            $pending = Review::whereNull('admin_reply_text')->whereNull('replied_at')->count();

            return [
                'average_rating' => $avgRounded,
                'average_delta_percent' => $avgDelta,
                'total_reviews' => $total,
                'total_delta_percent' => $totalChange,
                'new_7_days' => $new7,
                'pending_reply' => $pending,
                'total_users' => User::count(),
                'total_brands' => Brand::count(),
            ];
        });
    }

    /**
     * Detect which columns on places table can be used as display name.
     * Returns array of column names (excluding id).
     */
    private function detectPlaceDisplayColumns(): array
    {
        $candidates = [
            'name_en', 'name_ar',
            'title_en', 'title_ar',
            'name', 'title',
        ];

        $available = [];
        foreach ($candidates as $col) {
            if (Schema::hasColumn('places', $col)) {
                $available[] = $col;
            }
        }

        return $available;
    }

    /**
     * Return recent reviews mapped for the dashboard.
     * status: urgent|high|normal|all
     */
    public function getRecentReviews(?string $status = 'all', int $limit = 10): array
    {
        $cacheKey = "admin_recent_reviews_{$status}_{$limit}";
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($status, $limit) {
            $query = Review::query();

            // determine place display columns dynamically to avoid selecting missing columns
            $placeCols = $this->detectPlaceDisplayColumns();
            $withPlace = array_filter(array_merge(['id'], $placeCols));

            $query->with([
                'user:id,name,phone',
                // select only id and available display columns for place
                'place:' . implode(',', $withPlace),
                'branch:id,name',
            ])->withCount(['photos']);

            $query->orderBy('created_at', 'desc');

            $items = $query->limit($limit)->get();

            $negativeKeywords = [
                'bad','poor','terrible','awful','worst','hate','disappoint','rude','slow','late','missing',
            ];

            $now = Carbon::now();

            $mapped = $items->map(function (Review $r) use ($negativeKeywords, $now) {
                $name = $r->user?->name ?? 'Anonymous';
                $metaParts = [];
                $metaParts[] = $r->created_at?->diffForHumans();
                if ($r->branch) $metaParts[] = $r->branch->name;
                elseif ($r->place) $metaParts[] = $r->place?->display_name ?? ($r->place?->name ?? '-');

                $meta = implode(' • ', $metaParts);

                $text = $r->comment ?? '';

                $isPending = empty($r->admin_reply_text) && empty($r->replied_at);
                $pendingOver24 = $isPending && $r->created_at && $r->created_at->diffInHours($now) > 24;

                $containsNeg = Str::lower($text) && collect($negativeKeywords)->contains(fn($k) => str_contains(Str::lower($text), $k));

                // status rules
                $status = 'normal';
                if ($r->overall_rating <= 2 || $containsNeg || $pendingOver24) $status = 'urgent';
                elseif ($r->overall_rating == 3 || $isPending) $status = 'high';

                return [
                    'id' => $r->id,
                    'name' => $name,
                    'meta' => $meta,
                    'text' => $text,
                    'status' => $status,
                    'rating' => $r->overall_rating,
                    'helpful_count' => 0, // placeholder if not present
                    'photo_count' => $r->photos_count ?? 0,
                    'created_at' => $r->created_at,
                ];
            })->toArray();

            // Filter by requested status if not 'all'
            if ($status && $status !== 'all') {
                $mapped = array_values(array_filter($mapped, fn($i) => $i['status'] === $status));
            }

            // counts for chips (all/urgent/high/normal) — compute based on recent sample (could be distinct query if needed)
            $counts = [
                'all' => $items->count(),
                'urgent' => 0,
                'high' => 0,
                'normal' => 0,
            ];
            foreach ($mapped as $m) {
                $counts[$m['status']] = ($counts[$m['status']] ?? 0) + 1;
            }

            return ['items' => $mapped, 'counts' => $counts];
        });
    }
}
