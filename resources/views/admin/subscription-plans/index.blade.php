@extends('admin.layouts.app')

@section('page_title', __('admin.subscription_plans'))
@section('title', __('admin.subscription_plans'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.subscription_plans') }}</h2>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.subscription_plans_hint') }}</div>
      </div>
      <div class="flex items-center gap-3">
        <a href="{{ route('admin.subscription-plans.create') }}"
           class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">
          {{ __('admin.add_plan') }}
        </a>
      </div>
    </div>

    @if(session('success'))
      <div class="mt-4 rounded-2xl bg-green-50 border border-green-100 text-green-700 text-sm px-4 py-3">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="mt-4 rounded-2xl bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3">
        {{ session('error') }}
      </div>
    @endif

    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.plans_total') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.plans_active') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['active'] ?? 0 }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.plans_inactive') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['inactive'] ?? 0 }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.plans_best_value') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['best_value'] ?? 0 }}</div>
      </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-4 gap-3">
      <input name="q" value="{{ $q }}" placeholder="{{ __('admin.search') }}"
             class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      <select name="active" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <option value="">{{ __('admin.all') }}</option>
        <option value="1" {{ (string)$active === '1' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
        <option value="0" {{ (string)$active === '0' ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
      </select>
      <div class="md:col-span-2 flex items-center gap-2">
        <button type="submit" class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">
          {{ __('admin.filter') }}
        </button>
        <a href="{{ route('admin.subscription-plans.index') }}" class="text-sm text-gray-500">{{ __('admin.reset') }}</a>
      </div>
    </form>
  </div>

  <div class="mt-5 grid grid-cols-1 gap-4">
    @forelse($plans as $p)
      @php
        $price = number_format(($p->price_cents ?? 0) / 100, 2);
        $intervalLabel = $p->interval === 'year'
            ? ($p->interval_count > 1 ? __('admin.years') : __('admin.year'))
            : ($p->interval_count > 1 ? __('admin.months') : __('admin.month'));
        $trialLabel = (int) ($p->trial_days ?? 0) > 0 ? $p->trial_days : __('admin.no_trial');
        $description = $p->description_en ?: $p->description_ar;
      @endphp
      <div class="bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
          <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
              <span class="text-xs uppercase tracking-wide text-gray-500">{{ $p->code }}</span>
              <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $p->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                {{ $p->is_active ? __('admin.plan_status_active') : __('admin.plan_status_inactive') }}
              </span>
              @if($p->is_best_value)
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700">
                  {{ __('admin.best_value') }}
                </span>
              @endif
            </div>

            <div class="mt-2 text-lg font-semibold text-gray-900 truncate">{{ $p->name_en }}</div>
            <div class="text-xs text-gray-500 mt-1 truncate">{{ $p->name_ar ?? '-' }}</div>

            <div class="mt-3 flex flex-wrap items-center gap-4 text-sm text-gray-700">
              <span class="font-semibold">{{ $price }} {{ $p->currency }}</span>
              <span>&bull;</span>
              <span>{{ $p->interval_count }} {{ $intervalLabel }}</span>
              <span>&bull;</span>
              <span>{{ __('admin.trial_days') }}: {{ $trialLabel }}</span>
            </div>

            @if(!empty($description))
              <div class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $description }}</div>
            @endif
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <form method="POST" action="{{ route('admin.subscription-plans.best-value', $p) }}">
              @csrf
              @method('PATCH')
              <button type="submit"
                class="px-3 py-2 rounded-full border border-yellow-200 text-yellow-800 text-xs font-semibold hover:bg-yellow-50 transition">
                {{ $p->is_best_value ? __('admin.unset_best_value') : __('admin.set_best_value') }}
              </button>
            </form>

            <form method="POST" action="{{ route('admin.subscription-plans.toggle', $p) }}">
              @csrf
              @method('PATCH')
              <button type="submit"
                class="w-12 h-6 rounded-full {{ $p->is_active ? 'bg-green-500' : 'bg-gray-200' }} relative transition">
                <span class="absolute top-0.5 {{ $p->is_active ? 'left-6' : 'left-0.5' }} w-5 h-5 rounded-full bg-white transition"></span>
              </button>
            </form>

            <a href="{{ route('admin.subscription-plans.edit', $p) }}"
               class="w-9 h-9 rounded-full bg-white border border-gray-200 grid place-items-center text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21 7.5a2.5 2.5 0 0 0-2.5-2.5H7A2.5 2.5 0 0 0 4.5 7.5v9A2.5 2.5 0 0 0 7 19h10.5a2.5 2.5 0 0 0 2.5-2.5v-9Z"/>
              </svg>
            </a>

            <form method="POST" action="{{ route('admin.subscription-plans.destroy', $p) }}"
                  data-confirm="{{ __('admin.confirm_delete_plan') }}"
                  onsubmit="return confirm(this.dataset.confirm)">
              @csrf
              @method('DELETE')
              <button type="submit"
                class="w-9 h-9 rounded-full bg-white border border-red-200 grid place-items-center text-red-600 hover:bg-red-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M9 3h6l1 2h4v2H4V5h4l1-2Zm1 7h2v8h-2v-8Zm4 0h2v8h-2v-8ZM7 10h2v8H7v-8Z"/>
                </svg>
              </button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="bg-white border border-gray-100 rounded-[24px] p-10 text-center text-gray-500 shadow-soft">
        {{ __('admin.no_plans_found') }}
      </div>
    @endforelse
  </div>

  <div class="mt-6">
    {{ $plans->links() }}
  </div>
@endsection
