<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SubscriptionsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = (string) $request->get('status', '');
        $planId = (int) $request->get('plan_id', 0);
        $from = $request->get('from');
        $to = $request->get('to');

        $query = Subscription::query()
            ->with(['plan', 'user'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->whereHas('user', function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($status !== '', function ($qq) use ($status) {
                $qq->where('status', $status);
            })
            ->when($planId > 0, function ($qq) use ($planId) {
                $qq->where('subscription_plan_id', $planId);
            });

        if ($from) {
            $query->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }
        if ($to) {
            $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        $subs = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $statsBase = clone $query;
        $stats = [
            'total' => (int) $statsBase->count(),
            'free' => (int) (clone $statsBase)->where('status', 'FREE')->count(),
            'active' => (int) (clone $statsBase)->where('status', 'ACTIVE')->count(),
            'expired' => (int) (clone $statsBase)->where('status', 'EXPIRED')->count(),
        ];

        $plans = SubscriptionPlan::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.subscriptions.index', compact('subs', 'q', 'status', 'planId', 'from', 'to', 'stats', 'plans'));
    }
}
