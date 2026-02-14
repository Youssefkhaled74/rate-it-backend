<?php

namespace App\Services\Admin;

use App\Models\Review;
use App\Models\User;
use App\Models\Brand;
use App\Models\Branch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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
            $avgReviewScore = Review::whereBetween('created_at', [$periodStart, $periodEnd])->avg('review_score') ?: 0;
            $prevAvgReviewScore = Review::whereBetween('created_at', [$prevStart, $prevEnd])->avg('review_score') ?: 0;

            $avgRounded = round($avg, 1);
            $avgDelta = $prevAvg > 0 ? round((($avg - $prevAvg) / $prevAvg) * 100, 1) : null;
            $avgReviewScoreRounded = round($avgReviewScore, 2);
            $avgReviewScoreDelta = $prevAvgReviewScore > 0
                ? round((($avgReviewScore - $prevAvgReviewScore) / $prevAvgReviewScore) * 100, 1)
                : null;

            // Reviews last 30 days and percent change vs previous 30 days
            $totalAll = Review::count();
            $total30 = Review::whereBetween('created_at', [$periodStart, $periodEnd])->count();
            $totalPrev30 = Review::whereBetween('created_at', [$prevStart, $prevEnd])->count();
            $totalChange = $totalPrev30 > 0 ? round((($total30 - $totalPrev30) / max(1, $totalPrev30)) * 100, 1) : null;

            // New in last 7 days
            $new7 = Review::where('created_at', '>=', $now->copy()->subDays(7))->count();

            // Pending reply: reviews without admin_reply_text AND without replied_at
            $pending = Review::whereNull('admin_reply_text')->whereNull('replied_at')->count();

            return [
                'average_rating' => $avgRounded,
                'average_delta_percent' => $avgDelta,
                'average_review_score' => $avgReviewScoreRounded,
                'average_review_score_delta_percent' => $avgReviewScoreDelta,
                'total_reviews' => $total30,
                'total_reviews_all' => $totalAll,
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
                'branch:id,name,name_en,name_ar',
            ])->withCount(['photos']);

            $query->orderBy('created_at', 'desc');

            $items = $query->limit($limit)->get();

            $negativeKeywords = [
                // English
                'bad','poor','terrible','awful','worst','hate','disappoint','rude','slow','late','missing',
                // Arabic (common complaints)
                'سيء','سيئ','وحش','أسوأ','رديء','سيئة','خدمة سيئة','تجربة سيئة','بطيء','بطء','تأخير','تأخرت','قذر','غير محترم','غالي','مخيب','مخيبة',
            ];
            $now = Carbon::now();
            $hasHelpfulCount = Schema::hasColumn('reviews', 'helpful_count');

            $mapped = $items->map(function (Review $r) use ($negativeKeywords, $now, $hasHelpfulCount) {
                $name = $r->user?->name ?? 'Anonymous';
                $metaParts = [];
                $metaParts[] = $r->created_at?->diffForHumans();
                if ($r->branch) $metaParts[] = $r->branch->name;
                elseif ($r->place) $metaParts[] = $r->place?->display_name ?? ($r->place?->name ?? '-');

                $meta = implode(' • ', $metaParts);

                $text = $r->comment ?? '';
                $lowerText = Str::lower($text);

                $isPending = empty($r->admin_reply_text) && empty($r->replied_at);
                $pendingOver24 = $isPending && $r->created_at && $r->created_at->diffInHours($now) > 24;

                $containsNeg = $lowerText !== '' && collect($negativeKeywords)->contains(fn($k) => str_contains($lowerText, Str::lower($k)));

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
                    'overall_rating' => $r->overall_rating,
                    'review_score' => $r->review_score,
                    'helpful_count' => $hasHelpfulCount ? (int) ($r->helpful_count ?? 0) : 0,
                    'photo_count' => $r->photos_count ?? 0,
                    'created_at' => $r->created_at,
                    'url' => route('admin.reviews.show', $r),
                ];
            })->toArray();

            // counts for chips (all/urgent/high/normal) — compute based on recent sample (could be distinct query if needed)
            $counts = [
                'all' => count($mapped),
                'urgent' => 0,
                'high' => 0,
                'normal' => 0,
            ];
            foreach ($mapped as $m) {
                $counts[$m['status']] = ($counts[$m['status']] ?? 0) + 1;
            }

            // Filter by requested status if not 'all'
            if ($status && $status !== 'all') {
                $mapped = array_values(array_filter($mapped, fn($i) => $i['status'] === $status));
            }

            return ['items' => $mapped, 'counts' => $counts];
        });
    }

    /**
     * Return recent branches for dashboard.
     */
    public function getRecentBranches(int $limit = 6): array
    {
        return Cache::remember("admin_recent_branches_{$limit}", $this->cacheTtl, function () use ($limit) {
            $branches = Branch::query()
                ->with(['brand'])
                ->orderBy('id', 'desc')
                ->limit($limit)
                ->get();

            return $branches->map(function (Branch $b) {
                return [
                    'id' => $b->id,
                    'name' => $b->name ?: ($b->display_name ?? 'Branch'),
                    'brand' => $b->brand?->name_en ?? '',
                    'logo_url' => $b->logo_url,
                    'cover_url' => $b->cover_url,
                ];
            })->toArray();
        });
    }

    /**
     * Get reviews counts per day for the last N days (inclusive).
     */
    public function getReviewsChart(int $days = 7): array
    {
        $days = max(2, $days);
        $end = Carbon::today();
        $start = $end->copy()->subDays($days - 1);

        $rows = Review::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $values = [];

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('l');
            $values[] = (int) (($rows[$key]->count ?? 0));
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * Get user growth counts per month for the last N months (inclusive).
     */
    public function getUserGrowthMonthly(int $months = 12): array
    {
        $months = max(2, $months);
        $end = Carbon::now()->startOfMonth();
        $start = $end->copy()->subMonths($months - 1);

        $rows = User::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-01") as month, COUNT(*) as count')
            ->whereBetween('created_at', [$start->copy()->startOfMonth(), $end->copy()->endOfMonth()])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $values = [];

        $period = CarbonPeriod::create($start, '1 month', $end);
        foreach ($period as $date) {
            $key = $date->format('Y-m-01');
            $labels[] = $date->format('M');
            $values[] = (int) (($rows[$key]->count ?? 0));
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}

