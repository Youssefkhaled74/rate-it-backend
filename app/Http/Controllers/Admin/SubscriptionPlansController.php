<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;

class SubscriptionPlansController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $active = $request->get('active', '');

        $plans = SubscriptionPlan::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name_en', 'like', "%{$q}%")
                       ->orWhere('name_ar', 'like', "%{$q}%")
                       ->orWhere('code', 'like', "%{$q}%");
                });
            })
            ->when($active !== '', function ($query) use ($active) {
                $query->where('is_active', (bool) $active);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.subscription-plans.index', compact('plans', 'q', 'active'));
    }

    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatePlan($request, null);
        SubscriptionPlan::create($data);

        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', __('admin.plan_created'));
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.subscription-plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $data = $this->validatePlan($request, $plan->id);
        $plan->update($data);

        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', __('admin.plan_updated'));
    }

    public function toggle(SubscriptionPlan $plan)
    {
        $plan->is_active = ! $plan->is_active;
        $plan->save();

        return back()->with('success', __('admin.plan_updated'));
    }

    public function toggleBestValue(SubscriptionPlan $plan)
    {
        $plan->is_best_value = ! $plan->is_best_value;
        $plan->save();

        return back()->with('success', __('admin.plan_updated'));
    }

    public function destroy(SubscriptionPlan $plan)
    {
        $hasSubs = Subscription::where('subscription_plan_id', $plan->id)->exists();
        if ($hasSubs) {
            return back()->with('error', __('admin.plan_has_subscriptions'));
        }

        $plan->delete();
        return back()->with('success', __('admin.plan_deleted'));
    }

    private function validatePlan(Request $request, ?int $id): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:191', 'unique:subscription_plans,code'.($id ? ','.$id : '')],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:6'],
            'interval' => ['required', 'in:month,year'],
            'interval_count' => ['required', 'integer', 'min:1'],
            'trial_days' => ['nullable', 'integer', 'min:0'],
            'is_best_value' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $price = (float) ($data['price'] ?? 0);
        unset($data['price']);

        $data['price_cents'] = (int) round($price * 100);
        $data['trial_days'] = (int) ($data['trial_days'] ?? 0);
        $data['is_best_value'] = (bool) $request->boolean('is_best_value', false);
        $data['is_active'] = (bool) $request->boolean('is_active', true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
