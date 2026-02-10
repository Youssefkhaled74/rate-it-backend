<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchQrSession;
use App\Models\PointsTransaction;
use App\Models\Review;
use App\Models\Subscription;
use App\Models\SubscriptionTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KpiReportsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $fromDate = $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $toDate = $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay();

        // Subscription KPIs
        $subsQuery = Subscription::query();
        $subsRange = (clone $subsQuery)->whereBetween('created_at', [$fromDate, $toDate]);

        $subscriptions = [
            'total' => (int) $subsQuery->count(),
            'new_in_range' => (int) $subsRange->count(),
            'free' => (int) (clone $subsQuery)->where('status', 'FREE')->count(),
            'active' => (int) (clone $subsQuery)->where('status', 'ACTIVE')->count(),
            'expired' => (int) (clone $subsQuery)->where('status', 'EXPIRED')->count(),
        ];

        $transactionsRange = SubscriptionTransaction::query()
            ->whereBetween('created_at', [$fromDate, $toDate]);
        $subscriptions['revenue_cents'] = (int) $transactionsRange
            ->whereIn('status', ['paid', 'success', 'completed'])
            ->sum('amount_cents');

        // Points KPIs
        $pointsRange = PointsTransaction::query()
            ->whereBetween('created_at', [$fromDate, $toDate]);
        $pointsIssued = (int) (clone $pointsRange)->where('points', '>', 0)->sum('points');
        $pointsRedeemed = (int) abs((clone $pointsRange)->where('points', '<', 0)->sum('points'));
        $points = [
            'issued' => $pointsIssued,
            'redeemed' => $pointsRedeemed,
            'net' => $pointsIssued - $pointsRedeemed,
        ];

        // QR Scan Activity
        $qrRange = BranchQrSession::query()
            ->whereBetween('scanned_at', [$fromDate, $toDate]);
        $qr = [
            'total_scans' => (int) $qrRange->count(),
            'unique_users' => (int) (clone $qrRange)->distinct('user_id')->count('user_id'),
            'unique_branches' => (int) (clone $qrRange)->distinct('branch_id')->count('branch_id'),
        ];

        // Top & Low Rated Branches
        $reviewBase = Review::query()
            ->whereNotNull('branch_id')
            ->whereBetween('created_at', [$fromDate, $toDate]);

        $branchRatings = (clone $reviewBase)
            ->select('branch_id', DB::raw('AVG(overall_rating) as avg_rating'), DB::raw('COUNT(*) as reviews_count'))
            ->groupBy('branch_id');

        $topBranches = (clone $branchRatings)
            ->orderByDesc('avg_rating')
            ->orderByDesc('reviews_count')
            ->limit(5)
            ->get();

        $lowBranches = (clone $branchRatings)
            ->orderBy('avg_rating')
            ->orderByDesc('reviews_count')
            ->limit(5)
            ->get();

        $branchIds = $topBranches->pluck('branch_id')->merge($lowBranches->pluck('branch_id'))->unique();
        $branchesById = Branch::query()->whereIn('id', $branchIds)->get()->keyBy('id');

        return view('admin.kpi-reports.index', [
            'from' => $fromDate->format('Y-m-d'),
            'to' => $toDate->format('Y-m-d'),
            'subscriptions' => $subscriptions,
            'points' => $points,
            'qr' => $qr,
            'topBranches' => $topBranches,
            'lowBranches' => $lowBranches,
            'branchesById' => $branchesById,
        ]);
    }
}
