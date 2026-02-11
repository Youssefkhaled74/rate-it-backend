<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;

class DashboardReportsController extends Controller
{
    private function resolvePeriod(string $period, ?string $from = null, ?string $to = null): array
    {
        $now = Carbon::now();
        if ($from && $to) {
            $start = Carbon::parse($from)->startOfDay();
            $end = Carbon::parse($to)->endOfDay();
            return [$start, $end];
        }
        if ($period === 'month') {
            $start = $now->copy()->subDays(30)->startOfDay();
        } else {
            $start = $now->copy()->subDays(7)->startOfDay();
        }
        $end = $now->copy()->endOfDay();

        return [$start, $end];
    }

    private function buildReportData(string $period, ?string $from = null, ?string $to = null): array
    {
        [$start, $end] = $this->resolvePeriod($period, $from, $to);

        $reviewsQuery = Review::query()->whereBetween('created_at', [$start, $end]);

        $totalReviews = (int) $reviewsQuery->count();
        $avgRating = (float) ($reviewsQuery->avg('overall_rating') ?? 0);
        $newUsers = (int) User::whereBetween('created_at', [$start, $end])->count();
        $pendingReply = (int) Review::whereNull('admin_reply_text')->whereNull('replied_at')->count();

        $recentReviews = Review::query()
            ->with([
                'user:id,name',
                'branch:id,name,name_en,name_ar',
                'place',
            ])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return [
            'period' => $period,
            'start' => $start,
            'end' => $end,
            'total_reviews' => $totalReviews,
            'average_rating' => round($avgRating, 2),
            'new_users' => $newUsers,
            'pending_reply' => $pendingReply,
            'total_users' => (int) User::count(),
            'total_brands' => (int) Brand::count(),
            'recent_reviews' => $recentReviews,
        ];
    }

    public function csv(Request $request)
    {
        $period = $request->query('period', 'week');
        $period = in_array($period, ['week', 'month'], true) ? $period : 'week';
        $from = $request->query('from');
        $to = $request->query('to');

        $data = $this->buildReportData($period, $from, $to);

        $fileName = 'dashboard-report-' . $period . '-' . Carbon::now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($data) {
            $out = fopen('php://output', 'w');

            // Header
            fputcsv($out, ['Dashboard Report']);
            fputcsv($out, ['Period', $data['period']]);
            fputcsv($out, ['Start', $data['start']->toDateTimeString()]);
            fputcsv($out, ['End', $data['end']->toDateTimeString()]);
            fputcsv($out, []);

            // KPIs
            fputcsv($out, ['KPI', 'Value']);
            fputcsv($out, ['Total Reviews', $data['total_reviews']]);
            fputcsv($out, ['Average Rating', $data['average_rating']]);
            fputcsv($out, ['New Users', $data['new_users']]);
            fputcsv($out, ['Pending Reply', $data['pending_reply']]);
            fputcsv($out, ['Total Users', $data['total_users']]);
            fputcsv($out, ['Total Brands', $data['total_brands']]);
            fputcsv($out, []);

            // Recent reviews
            fputcsv($out, ['Recent Reviews']);
            fputcsv($out, ['ID', 'User', 'Rating', 'Comment', 'Branch', 'Place', 'Created At']);
            foreach ($data['recent_reviews'] as $r) {
                $placeName = $r->place?->display_name
                    ?? $r->place?->name
                    ?? $r->place?->name_en
                    ?? $r->place?->title_en
                    ?? $r->place?->name_ar
                    ?? $r->place?->title_ar
                    ?? '-';

                fputcsv($out, [
                    $r->id,
                    $r->user?->name ?? '-',
                    $r->overall_rating ?? '-',
                    $r->comment ?? '-',
                    $r->branch?->name ?? '-',
                    $placeName,
                    $r->created_at?->toDateTimeString() ?? '-',
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pdf(Request $request)
    {
        $period = $request->query('period', 'week');
        $period = in_array($period, ['week', 'month'], true) ? $period : 'week';
        $from = $request->query('from');
        $to = $request->query('to');

        $data = $this->buildReportData($period, $from, $to);

        $html = view('admin.reports.dashboard-pdf', $data)->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = 'dashboard-report-' . $period . '-' . Carbon::now()->format('Y-m-d') . '.pdf';

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

}
