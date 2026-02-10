@extends('admin.layouts.app')

@section('page_title', __('admin.kpi_reports'))
@section('title', __('admin.kpi_reports'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.kpi_reports') }}</h2>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.kpi_reports_hint') }}</div>
      </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-4 gap-3">
      <input type="date" name="from" value="{{ $from }}"
             class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      <input type="date" name="to" value="{{ $to }}"
             class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      <div class="md:col-span-2 flex items-center gap-2">
        <button type="submit" class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">
          {{ __('admin.filter') }}
        </button>
        <a href="{{ route('admin.kpi-reports.index') }}" class="text-sm text-gray-500">{{ __('admin.reset') }}</a>
      </div>
    </form>
  </div>

  <div class="mt-6 grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
      <div class="text-lg font-semibold text-gray-900">{{ __('admin.kpi_subscriptions') }}</div>
      <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-gray-700">
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">{{ __('admin.total') }}</div>
          <div class="text-lg font-semibold">{{ $subscriptions['total'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">{{ __('admin.new_in_range') }}</div>
          <div class="text-lg font-semibold">{{ $subscriptions['new_in_range'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">{{ __('admin.revenue') }}</div>
          <div class="text-lg font-semibold">
            {{ number_format(($subscriptions['revenue_cents'] ?? 0) / 100, 2) }}
          </div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">{{ __('admin.active') }}</div>
          <div class="text-lg font-semibold">{{ $subscriptions['active'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">{{ __('admin.free') }}</div>
          <div class="text-lg font-semibold">{{ $subscriptions['free'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">{{ __('admin.expired') }}</div>
          <div class="text-lg font-semibold">{{ $subscriptions['expired'] ?? 0 }}</div>
        </div>
      </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
      <div class="text-lg font-semibold text-gray-900">{{ __('admin.kpi_points') }}</div>
      <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-gray-700">
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">{{ __('admin.points_issued') }}</div>
          <div class="text-lg font-semibold">{{ $points['issued'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">{{ __('admin.points_redeemed') }}</div>
          <div class="text-lg font-semibold">{{ $points['redeemed'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">{{ __('admin.points_net') }}</div>
          <div class="text-lg font-semibold">{{ $points['net'] ?? 0 }}</div>
        </div>
      </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
      <div class="text-lg font-semibold text-gray-900">{{ __('admin.kpi_qr_scans') }}</div>
      <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-gray-700">
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">{{ __('admin.qr_scans_total') }}</div>
          <div class="text-lg font-semibold">{{ $qr['total_scans'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">{{ __('admin.qr_unique_users') }}</div>
          <div class="text-lg font-semibold">{{ $qr['unique_users'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">{{ __('admin.qr_unique_branches') }}</div>
          <div class="text-lg font-semibold">{{ $qr['unique_branches'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-6 grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
      <div class="text-lg font-semibold text-gray-900">{{ __('admin.top_branches') }}</div>
      <div class="mt-4 space-y-3">
        @forelse($topBranches as $row)
          @php $branch = $branchesById[$row->branch_id] ?? null; @endphp
          <div class="rounded-2xl border border-gray-100 px-4 py-3 flex items-center justify-between">
            <div>
              <div class="font-semibold text-gray-900">{{ $branch?->name ?? ('#'.$row->branch_id) }}</div>
              <div class="text-xs text-gray-500">{{ __('admin.reviews') }}: {{ $row->reviews_count }}</div>
            </div>
            <div class="text-sm font-semibold text-emerald-700">{{ number_format((float)$row->avg_rating, 2) }}</div>
          </div>
        @empty
          <div class="text-sm text-gray-500">{{ __('admin.no_data') }}</div>
        @endforelse
      </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
      <div class="text-lg font-semibold text-gray-900">{{ __('admin.low_branches') }}</div>
      <div class="mt-4 space-y-3">
        @forelse($lowBranches as $row)
          @php $branch = $branchesById[$row->branch_id] ?? null; @endphp
          <div class="rounded-2xl border border-gray-100 px-4 py-3 flex items-center justify-between">
            <div>
              <div class="font-semibold text-gray-900">{{ $branch?->name ?? ('#'.$row->branch_id) }}</div>
              <div class="text-xs text-gray-500">{{ __('admin.reviews') }}: {{ $row->reviews_count }}</div>
            </div>
            <div class="text-sm font-semibold text-red-700">{{ number_format((float)$row->avg_rating, 2) }}</div>
          </div>
        @empty
          <div class="text-sm text-gray-500">{{ __('admin.no_data') }}</div>
        @endforelse
      </div>
    </div>
  </div>
@endsection
