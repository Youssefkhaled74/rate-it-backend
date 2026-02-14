<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\BranchQrSession;
use App\Models\PointsTransaction;
use App\Models\Review;
use App\Models\Subscription;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KpiReportsController extends Controller
{
    public function index(Request $request)
    {
        [$fromDate, $toDate] = $this->resolveDateRange($request);
        $data = $this->buildData($fromDate, $toDate);

        return view('admin.kpi-reports.index', [
            'from' => $fromDate->format('Y-m-d'),
            'to' => $toDate->format('Y-m-d'),
            ...$data,
        ]);
    }

    public function exportXlsx(Request $request)
    {
        [$fromDate, $toDate] = $this->resolveDateRange($request);
        $data = $this->buildData($fromDate, $toDate);

        $spreadsheet = new Spreadsheet();

        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Summary');
        $summaryRows = [
            ['KPI', 'Value'],
            ['From', $fromDate->toDateTimeString()],
            ['To', $toDate->toDateTimeString()],
            ['Total Reviews', $data['overview']['total_reviews']],
            ['Average Overall Rating', $data['overview']['avg_overall_rating']],
            ['Average Review Score', $data['overview']['avg_review_score']],
            ['Pending Reply', $data['overview']['pending_reply']],
            ['Reply Rate %', $data['overview']['reply_rate_percent']],
            ['Average Reply Hours', $data['overview']['avg_reply_hours']],
            ['Hidden Reviews', $data['overview']['hidden_reviews']],
            ['Featured Reviews', $data['overview']['featured_reviews']],
            ['New Users', $data['overview']['new_users']],
            ['Active Users', $data['overview']['active_users']],
            ['Total Users', $data['overview']['total_users']],
            ['Total Brands', $data['overview']['total_brands']],
            ['Total Branches', $data['overview']['total_branches']],
            ['Top Brand (by rating)', $data['overview']['top_brand_name'] ?? '-'],
            ['Top Brand Rating', $data['overview']['top_brand_rating'] ?? 0],
        ];
        $summarySheet->fromArray($summaryRows, null, 'A1');
        $summarySheet->getStyle('A1:B1')->getFont()->setBold(true);
        $summarySheet->getColumnDimension('A')->setAutoSize(true);
        $summarySheet->getColumnDimension('B')->setAutoSize(true);

        $this->appendTableSheet($spreadsheet, 'Top Branches', ['Branch', 'Avg Rating', 'Reviews'], $data['topBranches']->map(function ($row) use ($data) {
            $branch = $data['branchesById'][$row->branch_id] ?? null;
            return [
                $branch?->name_en ?? $branch?->name_ar ?? $branch?->name ?? ('#' . $row->branch_id),
                round((float) $row->avg_rating, 2),
                (int) $row->reviews_count,
            ];
        })->toArray());

        $this->appendTableSheet($spreadsheet, 'Low Branches', ['Branch', 'Avg Rating', 'Reviews'], $data['lowBranches']->map(function ($row) use ($data) {
            $branch = $data['branchesById'][$row->branch_id] ?? null;
            return [
                $branch?->name_en ?? $branch?->name_ar ?? $branch?->name ?? ('#' . $row->branch_id),
                round((float) $row->avg_rating, 2),
                (int) $row->reviews_count,
            ];
        })->toArray());

        $this->appendTableSheet($spreadsheet, 'Top Brands', ['Brand', 'Avg Rating', 'Reviews'], $data['topBrands']->map(function ($row) {
            return [
                $row->name_en ?? $row->name ?? $row->name_ar ?? ('#' . $row->brand_id),
                round((float) $row->avg_rating, 2),
                (int) $row->reviews_count,
            ];
        })->toArray());

        $this->appendTableSheet($spreadsheet, 'Top Users', ['User', 'Reviews', 'Avg Rating'], $data['topUsers']->map(function ($row) {
            return [
                $row->user_name ?? ('#' . $row->user_id),
                (int) $row->reviews_count,
                round((float) $row->avg_rating, 2),
            ];
        })->toArray());

        $writer = new Xlsx($spreadsheet);
        $tmp = tempnam(sys_get_temp_dir(), 'kpi_') . '.xlsx';
        $writer->save($tmp);

        $fileName = 'kpi-report-' . $fromDate->format('Ymd') . '-' . $toDate->format('Ymd') . '.xlsx';

        return response()->download($tmp, $fileName)->deleteFileAfterSend(true);
    }

    public function exportPdf(Request $request)
    {
        [$fromDate, $toDate] = $this->resolveDateRange($request);
        $data = $this->buildData($fromDate, $toDate);

        $html = view('admin.reports.kpi-pdf', [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            ...$data,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = 'kpi-report-' . $fromDate->format('Ymd') . '-' . $toDate->format('Ymd') . '.pdf';

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    private function resolveDateRange(Request $request): array
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $fromDate = $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $toDate = $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay();

        if ($toDate->lt($fromDate)) {
            [$fromDate, $toDate] = [$toDate->copy()->startOfDay(), $fromDate->copy()->endOfDay()];
        }

        return [$fromDate, $toDate];
    }

    private function buildData(Carbon $fromDate, Carbon $toDate): array
    {
        $subsQuery = Subscription::query();
        $subsRange = (clone $subsQuery)->whereBetween('created_at', [$fromDate, $toDate]);
        $transactionsRange = SubscriptionTransaction::query()->whereBetween('created_at', [$fromDate, $toDate]);

        $subscriptions = [
            'total' => (int) $subsQuery->count(),
            'new_in_range' => (int) $subsRange->count(),
            'free' => (int) (clone $subsQuery)->where('status', 'FREE')->count(),
            'active' => (int) (clone $subsQuery)->where('status', 'ACTIVE')->count(),
            'expired' => (int) (clone $subsQuery)->where('status', 'EXPIRED')->count(),
            'revenue_cents' => (int) (clone $transactionsRange)->whereIn('status', ['paid', 'success', 'completed'])->sum('amount_cents'),
            'payments_count' => (int) (clone $transactionsRange)->whereIn('status', ['paid', 'success', 'completed'])->count(),
        ];

        $pointsRange = PointsTransaction::query()->whereBetween('created_at', [$fromDate, $toDate]);
        $pointsIssued = (int) (clone $pointsRange)->where('points', '>', 0)->sum('points');
        $pointsRedeemed = (int) abs((clone $pointsRange)->where('points', '<', 0)->sum('points'));
        $points = [
            'issued' => $pointsIssued,
            'redeemed' => $pointsRedeemed,
            'net' => $pointsIssued - $pointsRedeemed,
            'transactions_count' => (int) (clone $pointsRange)->count(),
        ];

        $qrRange = BranchQrSession::query()->whereBetween('scanned_at', [$fromDate, $toDate]);
        $totalScans = (int) (clone $qrRange)->count();
        $days = max(1, $fromDate->diffInDays($toDate) + 1);
        $qr = [
            'total_scans' => $totalScans,
            'unique_users' => (int) (clone $qrRange)->distinct('user_id')->count('user_id'),
            'unique_branches' => (int) (clone $qrRange)->distinct('branch_id')->count('branch_id'),
            'avg_scans_per_day' => round($totalScans / $days, 2),
        ];

        $reviewsRange = Review::query()->whereBetween('reviews.created_at', [$fromDate, $toDate]);
        $totalReviews = (int) (clone $reviewsRange)->count();
        $repliedReviews = (int) (clone $reviewsRange)->whereNotNull('reviews.replied_at')->count();

        $hiddenReviews = 0;
        if (Schema::hasColumn('reviews', 'is_hidden')) {
            $hiddenReviews = (int) (clone $reviewsRange)->where('reviews.is_hidden', true)->count();
        }

        $featuredReviews = 0;
        if (Schema::hasColumn('reviews', 'is_featured')) {
            $featuredReviews = (int) (clone $reviewsRange)->where('reviews.is_featured', true)->count();
        }

        $replyHours = null;
        if (DB::getDriverName() === 'mysql') {
            $replyHours = (float) ((clone $reviewsRange)
                ->whereNotNull('reviews.replied_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, reviews.created_at, reviews.replied_at)) as avg_hours')
                ->value('avg_hours') ?? 0);
        }

        $topBrand = $this->resolveTopBrand($fromDate, $toDate);

        $overview = [
            'total_reviews' => $totalReviews,
            'avg_overall_rating' => round((float) ((clone $reviewsRange)->avg('overall_rating') ?? 0), 2),
            'avg_review_score' => round((float) ((clone $reviewsRange)->avg('review_score') ?? 0), 2),
            'pending_reply' => (int) (clone $reviewsRange)->whereNull('reviews.admin_reply_text')->whereNull('reviews.replied_at')->count(),
            'reply_rate_percent' => $totalReviews > 0 ? round(($repliedReviews / $totalReviews) * 100, 1) : 0.0,
            'avg_reply_hours' => round((float) ($replyHours ?? 0), 2),
            'hidden_reviews' => $hiddenReviews,
            'featured_reviews' => $featuredReviews,
            'new_users' => (int) User::query()->whereBetween('created_at', [$fromDate, $toDate])->count(),
            'active_users' => (int) User::query()->whereNotNull('phone_verified_at')->count(),
            'total_users' => (int) User::query()->count(),
            'total_brands' => (int) Brand::query()->count(),
            'total_branches' => (int) Branch::query()->count(),
            'top_brand_name' => $topBrand['name'],
            'top_brand_rating' => $topBrand['rating'],
        ];

        $reviewBase = Review::query()
            ->whereNotNull('reviews.branch_id')
            ->whereBetween('reviews.created_at', [$fromDate, $toDate]);

        if (Schema::hasColumn('reviews', 'is_hidden')) {
            $reviewBase->where(function ($q) {
                $q->whereNull('reviews.is_hidden')->orWhere('reviews.is_hidden', false);
            });
        }

        $branchRatings = (clone $reviewBase)
            ->select('branch_id', DB::raw('AVG(overall_rating) as avg_rating'), DB::raw('COUNT(*) as reviews_count'))
            ->groupBy('branch_id')
            ->havingRaw('COUNT(*) >= 2');

        $topBranches = (clone $branchRatings)->orderByDesc('avg_rating')->orderByDesc('reviews_count')->limit(5)->get();
        $lowBranches = (clone $branchRatings)->orderBy('avg_rating')->orderByDesc('reviews_count')->limit(5)->get();

        $branchIds = $topBranches->pluck('branch_id')->merge($lowBranches->pluck('branch_id'))->unique()->values();
        $branchesById = Branch::query()->whereIn('id', $branchIds)->get()->keyBy('id');

        $topBrands = (clone $reviewBase)
            ->join('branches', 'branches.id', '=', 'reviews.branch_id')
            ->join('brands', 'brands.id', '=', 'branches.brand_id')
            ->groupBy('branches.brand_id', 'brands.name_en', 'brands.name_ar')
            ->selectRaw('branches.brand_id as brand_id, brands.name_en, brands.name_ar, AVG(reviews.overall_rating) as avg_rating, COUNT(reviews.id) as reviews_count')
            ->havingRaw('COUNT(reviews.id) >= 2')
            ->orderByDesc('avg_rating')
            ->orderByDesc('reviews_count')
            ->limit(5)
            ->get();

        $topUsers = (clone $reviewsRange)
            ->join('users', 'users.id', '=', 'reviews.user_id')
            ->groupBy('reviews.user_id', 'users.name')
            ->selectRaw('reviews.user_id as user_id, users.name as user_name, COUNT(reviews.id) as reviews_count, AVG(reviews.overall_rating) as avg_rating')
            ->orderByDesc('reviews_count')
            ->orderByDesc('avg_rating')
            ->limit(5)
            ->get();

        return compact(
            'subscriptions',
            'points',
            'qr',
            'overview',
            'topBranches',
            'lowBranches',
            'branchesById',
            'topBrands',
            'topUsers'
        );
    }

    private function resolveTopBrand(Carbon $fromDate, Carbon $toDate): array
    {
        $query = Review::query()
            ->join('branches', 'branches.id', '=', 'reviews.branch_id')
            ->join('brands', 'brands.id', '=', 'branches.brand_id')
            ->whereBetween('reviews.created_at', [$fromDate, $toDate]);

        if (Schema::hasColumn('reviews', 'is_hidden')) {
            $query->where(function ($q) {
                $q->whereNull('reviews.is_hidden')->orWhere('reviews.is_hidden', false);
            });
        }

        $row = $query->groupBy('branches.brand_id', 'brands.name_en', 'brands.name_ar')
            ->selectRaw('branches.brand_id as brand_id, brands.name_en, brands.name_ar, AVG(reviews.overall_rating) as avg_rating, COUNT(reviews.id) as reviews_count')
            ->orderByDesc('avg_rating')
            ->orderByDesc('reviews_count')
            ->first();

        return [
            'name' => $row ? ($row->name_en ?: ($row->name_ar ?: ('#' . $row->brand_id))) : null,
            'rating' => $row ? round((float) $row->avg_rating, 2) : null,
        ];
    }

    private function appendTableSheet(Spreadsheet $spreadsheet, string $title, array $headers, array $rows): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle($title);
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:' . chr(64 + count($headers)) . '1')->getFont()->setBold(true);
        if (!empty($rows)) {
            $sheet->fromArray($rows, null, 'A2');
        }
        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getColumnDimension(chr(64 + $i))->setAutoSize(true);
        }
    }
}
