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
      <div class="flex items-center gap-2">
        <a href="{{ route('admin.kpi-reports.export.xlsx', request()->query()) }}"
           class="rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:border-gray-300 inline-flex items-center gap-1.5">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <path d="M14 2v6h6"/>
            <path d="M9 12l6 6M15 12l-6 6"/>
          </svg>
          <span>Excel</span>
        </a>
        <a href="{{ route('admin.kpi-reports.export.pdf', request()->query()) }}"
           class="rounded-full border border-red-200 px-4 py-2 text-sm font-semibold text-red-700 hover:border-red-300 inline-flex items-center gap-1.5">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <path d="M14 2v6h6"/>
            <path d="M8 13h4a2 2 0 0 1 0 4H8z"/>
            <path d="M14 17v-4h2a2 2 0 0 1 0 4h-2z"/>
          </svg>
          <span>PDF</span>
        </a>
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

  <div class="mt-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-xs text-gray-500">Total Reviews (Range)</div>
      <div class="mt-2 text-2xl font-semibold text-gray-900">{{ $overview['total_reviews'] ?? 0 }}</div>
      <div class="text-[11px] text-gray-500 mt-1">Avg Overall: {{ number_format((float) ($overview['avg_overall_rating'] ?? 0), 2) }}</div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-xs text-gray-500">Avg Review Score</div>
      <div class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format((float) ($overview['avg_review_score'] ?? 0), 2) }}</div>
      <div class="text-[11px] text-gray-500 mt-1">Reply Rate: {{ number_format((float) ($overview['reply_rate_percent'] ?? 0), 1) }}%</div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-xs text-gray-500">Pending Reply</div>
      <div class="mt-2 text-2xl font-semibold text-gray-900">{{ $overview['pending_reply'] ?? 0 }}</div>
      <div class="text-[11px] text-gray-500 mt-1">Avg Reply Hours: {{ number_format((float) ($overview['avg_reply_hours'] ?? 0), 2) }}</div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-xs text-gray-500">Top Brand By Rating</div>
      <div class="mt-2 text-lg font-semibold text-gray-900 truncate">{{ $overview['top_brand_name'] ?? '-' }}</div>
      <div class="text-[11px] text-gray-500 mt-1">Rating: {{ number_format((float) ($overview['top_brand_rating'] ?? 0), 2) }}</div>
    </div>
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
          <div class="text-lg font-semibold">{{ number_format(($subscriptions['revenue_cents'] ?? 0) / 100, 2) }}</div>
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
      <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3 text-sm text-gray-700">
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
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">Transactions</div>
          <div class="text-lg font-semibold">{{ $points['transactions_count'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-6 grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
      <div class="text-lg font-semibold text-gray-900">{{ __('admin.kpi_qr_scans') }}</div>
      <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3 text-sm text-gray-700">
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
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">Avg / Day</div>
          <div class="text-lg font-semibold">{{ number_format((float) ($qr['avg_scans_per_day'] ?? 0), 2) }}</div>
        </div>
      </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
      <div class="text-lg font-semibold text-gray-900">Moderation Health</div>
      <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-gray-700">
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">Hidden Reviews</div>
          <div class="text-lg font-semibold">{{ $overview['hidden_reviews'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">Featured Reviews</div>
          <div class="text-lg font-semibold">{{ $overview['featured_reviews'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
          <div class="text-xs text-gray-500">New Users (Range)</div>
          <div class="text-lg font-semibold">{{ $overview['new_users'] ?? 0 }}</div>
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
              <div class="font-semibold text-gray-900">{{ $branch?->name_en ?? $branch?->name_ar ?? $branch?->name ?? ('#'.$row->branch_id) }}</div>
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
              <div class="font-semibold text-gray-900">{{ $branch?->name_en ?? $branch?->name_ar ?? $branch?->name ?? ('#'.$row->branch_id) }}</div>
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

  <div class="mt-6 grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
      <div class="text-lg font-semibold text-gray-900">Top Brands (By Rating)</div>
      <div class="mt-4 space-y-3">
        @forelse($topBrands as $row)
          <div class="rounded-2xl border border-gray-100 px-4 py-3 flex items-center justify-between">
            <div>
              <div class="font-semibold text-gray-900">{{ $row->name_en ?? $row->name_ar ?? ('#'.$row->brand_id) }}</div>
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
      <div class="text-lg font-semibold text-gray-900">Top Users (By Reviews)</div>
      <div class="mt-4 space-y-3">
        @forelse($topUsers as $row)
          <div class="rounded-2xl border border-gray-100 px-4 py-3 flex items-center justify-between">
            <div>
              <div class="font-semibold text-gray-900">{{ $row->user_name ?? ('#'.$row->user_id) }}</div>
              <div class="text-xs text-gray-500">{{ __('admin.reviews') }}: {{ $row->reviews_count }}</div>
            </div>
            <div class="text-sm font-semibold text-blue-700">{{ number_format((float)$row->avg_rating, 2) }}</div>
          </div>
        @empty
          <div class="text-sm text-gray-500">{{ __('admin.no_data') }}</div>
        @endforelse
      </div>
    </div>
  </div>
@endsection
