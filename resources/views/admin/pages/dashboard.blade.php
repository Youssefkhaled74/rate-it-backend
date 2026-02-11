@extends('admin.layouts.app')

@section('page_title', __('admin.dashboard'))
@section('title', __('admin.dashboard_title'))

@section('content')
  {{-- Hero --}}
  <div class="bg-gradient-to-r from-white via-white to-rose-50 rounded-[28px] shadow-soft p-6 border border-rose-100/60 relative overflow-hidden">
    <div class="pointer-events-none absolute -right-6 -top-6 hidden md:block opacity-80">
      <svg width="210" height="120" viewBox="0 0 210 120" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M45 12c4 15 7 18 22 22-15 4-18 7-22 22-4-15-7-18-22-22 15-4 18-7 22-22Z" fill="#F9C6C6"/>
        <path d="M110 6c3 12 6 15 18 18-12 3-15 6-18 18-3-12-6-15-18-18 12-3 15-6 18-18Z" fill="#F4AAAA"/>
        <path d="M160 24c3 11 5 14 16 16-11 3-14 5-16 16-3-11-5-14-16-16 11-2 14-5 16-16Z" fill="#F7B2B2"/>
        <path d="M182 64c2 8 4 10 12 12-8 2-10 4-12 12-2-8-4-10-12-12 8-2 10-4 12-12Z" fill="#FAD3D3"/>
      </svg>
    </div>

    <div class="flex items-center justify-between gap-6">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-gray-100 overflow-hidden ring-2 ring-white shadow-sm">
          <img src="{{ asset('assets/images/userdefultphoto.png') }}" alt="avatar" class="w-12 h-12 object-cover">
        </div>
        <div>
          <div class="text-xs text-gray-500">
            {{ __('admin.good_morning') }}
            <span class="text-red-700 font-semibold">{{ $welcomeName ?? __('admin.admin') }}</span>
          </div>
          @php $headlineCount = (int) ($counts['all'] ?? 0); @endphp
          <div class="text-sm text-gray-700 mt-2">
            {!! __('admin.dashboard_headline', ['count' => '<span class="text-red-700 font-semibold">' . $headlineCount . '</span>']) !!}
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- Quick Stats --}}
  <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-xs text-gray-500">{{ __('admin.total_users') }}</div>
      <div class="mt-2 flex items-baseline justify-between">
        <div class="text-2xl font-semibold text-gray-900">{{ $stats['total_users'] ?? 0 }}</div>
        <div class="text-[11px] text-gray-400">{{ __('admin.new_7_days') }}: {{ $stats['new_7_days'] ?? 0 }}</div>
      </div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-xs text-gray-500">{{ __('admin.total_brands') }}</div>
      <div class="mt-2 flex items-baseline justify-between">
        <div class="text-2xl font-semibold text-gray-900">{{ $stats['total_brands'] ?? 0 }}</div>
        <div class="text-[11px] text-gray-400">{{ __('admin.pending_reply') }}: {{ $stats['pending_reply'] ?? 0 }}</div>
      </div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-xs text-gray-500">{{ __('admin.total_reviews') }}</div>
      <div class="mt-2 flex items-baseline justify-between">
        <div class="text-2xl font-semibold text-gray-900">{{ $stats['total_reviews'] ?? 0 }}</div>
        <div class="text-[11px] text-gray-400">30d</div>
      </div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-xs text-gray-500">{{ __('admin.average_rating') }}</div>
      <div class="mt-2 flex items-baseline justify-between">
        <div class="text-2xl font-semibold text-gray-900">{{ $stats['average_rating'] ?? '0.0' }}</div>
        @if(!is_null($stats['average_delta_percent'] ?? null))
          <div class="text-[11px] {{ ($stats['average_delta_percent'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
            {{ ($stats['average_delta_percent'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['average_delta_percent'] ?? 0 }}%
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-xs text-gray-500">{{ __('admin.total_reviews') }}</div>
      <div class="mt-2 flex items-baseline justify-between">
        <div class="text-2xl font-semibold text-gray-900">{{ $stats['total_reviews_all'] ?? 0 }}</div>
        <div class="text-[11px] text-gray-400">{{ __('admin.all') }}</div>
      </div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-xs text-gray-500">{{ __('admin.pending_reply') }}</div>
      <div class="mt-2 flex items-baseline justify-between">
        <div class="text-2xl font-semibold text-gray-900">{{ $stats['pending_reply'] ?? 0 }}</div>
        <div class="text-[11px] text-gray-400">{{ __('admin.reviews') }}</div>
      </div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-xs text-gray-500">{{ __('admin.new_7_days') }}</div>
      <div class="mt-2 flex items-baseline justify-between">
        <div class="text-2xl font-semibold text-gray-900">{{ $stats['new_7_days'] ?? 0 }}</div>
        @if(!is_null($stats['total_delta_percent'] ?? null))
          <div class="text-[11px] {{ ($stats['total_delta_percent'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
            {{ ($stats['total_delta_percent'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['total_delta_percent'] ?? 0 }}%
          </div>
        @endif
      </div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-xs text-gray-500">{{ __('admin.subscription_free_days') }}</div>
      <div class="mt-2 flex items-baseline justify-between">
        <div class="text-2xl font-semibold text-gray-900">{{ $freeTrialDays ?? 0 }}</div>
        <div class="text-[11px] text-gray-400">{{ __('admin.days') }}</div>
      </div>
    </div>
  </div>

  {{-- Quick Access --}}
  <div class="mt-5 bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
    <div class="flex items-center justify-between">
      <div class="text-sm font-semibold text-gray-900">{{ __('admin.quick_access') }}</div>
      <div class="text-xs text-gray-400">{{ __('admin.manage') }}</div>
    </div>
    <div class="mt-4 grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
      <a href="{{ route('admin.reviews.index') }}" class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 hover:border-gray-200 hover:bg-white transition">
        {{ __('admin.reviews') }}
      </a>
      <a href="{{ route('admin.users.index') }}" class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 hover:border-gray-200 hover:bg-white transition">
        {{ __('admin.users') }}
      </a>
      <a href="{{ route('admin.brands.index') }}" class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 hover:border-gray-200 hover:bg-white transition">
        {{ __('admin.brands') }}
      </a>
      <a href="{{ route('admin.places.index') }}" class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 hover:border-gray-200 hover:bg-white transition">
        {{ __('admin.places') }}
      </a>
      <a href="{{ route('admin.branches.index') }}" class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 hover:border-gray-200 hover:bg-white transition">
        {{ __('admin.branches') }}
      </a>
      <a href="{{ route('admin.subscription-plans.index') }}" class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 hover:border-gray-200 hover:bg-white transition">
        {{ __('admin.subscription_plans') }}
      </a>
      <a href="{{ route('admin.subscriptions.index') }}" class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 hover:border-gray-200 hover:bg-white transition">
        {{ __('admin.subscriptions') }}
      </a>
      <a href="{{ route('admin.rewards.index') }}" class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 hover:border-gray-200 hover:bg-white transition">
        {{ __('admin.rewards_system') }}
      </a>
      <a href="{{ route('admin.kpi-reports.index') }}" class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 hover:border-gray-200 hover:bg-white transition">
        {{ __('admin.kpi_reports') }}
      </a>
      <a href="{{ route('admin.qr-management.index') }}" class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 hover:border-gray-200 hover:bg-white transition">
        {{ __('admin.qr_management') }}
      </a>
      <a href="{{ route('admin.vouchers.index') }}" class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 hover:border-gray-200 hover:bg-white transition">
        {{ __('admin.vouchers') }}
      </a>
      <a href="{{ route('admin.search-suggestions.index') }}" class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 hover:border-gray-200 hover:bg-white transition">
        {{ __('admin.search_suggestions') }}
      </a>
    </div>
  </div>

  {{-- Charts Row --}}
  <div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
      <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold text-gray-900">{{ __('admin.reviews_over_time') }}</div>
        <form method="GET" class="flex items-center gap-2">
          <span class="text-xs text-gray-400">{{ __('admin.filter') }}</span>
          <select name="status" class="text-xs border border-gray-200 rounded-full px-3 py-1 focus:outline-none focus:ring-2 focus:ring-red-200" onchange="this.form.submit()">
            <option value="all" {{ ($selectedStatus ?? 'all') === 'all' ? 'selected' : '' }}>{{ __('admin.all') }}</option>
            <option value="urgent" {{ ($selectedStatus ?? 'all') === 'urgent' ? 'selected' : '' }}>{{ __('admin.urgent') }}</option>
            <option value="high" {{ ($selectedStatus ?? 'all') === 'high' ? 'selected' : '' }}>{{ __('admin.high') }}</option>
            <option value="normal" {{ ($selectedStatus ?? 'all') === 'normal' ? 'selected' : '' }}>{{ __('admin.normal') }}</option>
          </select>
        </form>
      </div>
      @php
        $chartLabels = $reviewsChart['labels'] ?? [];
        $chartValues = $reviewsChart['values'] ?? [];
        $chartCols = max(1, count($chartLabels));
      @endphp
      <div class="mt-4 rounded-2xl bg-white border border-gray-100 p-4 relative">
        <div id="reviewsNoData" class="hidden absolute inset-0 grid place-items-center text-sm text-gray-400">
          {{ __('admin.no_data') }}
        </div>
        <div id="reviewsChart" data-values='@json($chartValues)'>
          <svg class="w-full h-52" viewBox="0 0 640 200" preserveAspectRatio="none">
            <defs>
              <linearGradient id="reviewsAreaFill" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#b91c1c" stop-opacity="0.35"/>
                <stop offset="100%" stop-color="#b91c1c" stop-opacity="0"/>
              </linearGradient>
              <filter id="reviewsGlow" x="-20%" y="-20%" width="140%" height="140%">
                <feDropShadow dx="0" dy="6" stdDeviation="6" flood-color="#b91c1c" flood-opacity="0.15"/>
              </filter>
            </defs>
            <path id="reviewsArea" d="" fill="url(#reviewsAreaFill)"></path>
            <path id="reviewsLine" d="" fill="none" stroke="#b91c1c" stroke-width="3" stroke-linecap="round" filter="url(#reviewsGlow)"></path>
            <g id="reviewsDots"></g>
          </svg>
        </div>
        <div class="mt-2 text-[11px] text-gray-500 grid gap-1" data-chart-cols="{{ $chartCols }}">
          @foreach($chartLabels as $label)
            <div class="text-center">{{ $label }}</div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
      <div class="flex items-center justify-between">
        <div class="text-sm font-semibold text-gray-900">{{ __('admin.user_growth') }}</div>
        <div class="text-xs text-gray-400">{{ __('admin.last_12_months') }}</div>
      </div>
      @php
        $growthLabels = $userGrowth['labels'] ?? [];
        $growthValues = $userGrowth['values'] ?? [];
        $growthMax = max(1, ...($growthValues ?: [1]));
      @endphp
      <div class="mt-4 space-y-3">
        @foreach($growthLabels as $i => $label)
          @php $value = (int) ($growthValues[$i] ?? 0); @endphp
          <div class="flex items-center gap-3">
            <div class="w-8 text-xs text-gray-500">{{ $label }}</div>
            <div class="flex-1 h-2 rounded-full bg-gray-100 overflow-hidden">
              <div class="h-2 rounded-full bg-red-800 js-bar" data-width="{{ round(($value / $growthMax) * 100) }}"></div>
            </div>
            <div class="text-[10px] text-gray-400">( {{ $value }} )</div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Bottom Row --}}
  <div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
      <div class="flex items-center justify-between gap-3">
        <div class="text-sm font-semibold text-gray-900">{{ __('admin.recent_reviews_moderation') }}</div>
        @php
          $from = request('from');
          $to = request('to');
          $exportQuery = array_filter(['from' => $from, 'to' => $to]);
        @endphp
        <div class="flex items-center gap-2">
          <form method="GET" class="flex items-center gap-2">
            <input type="date" name="from" value="{{ $from }}" class="text-xs border border-gray-200 rounded-full px-3 py-1 focus:outline-none focus:ring-2 focus:ring-red-200">
            <input type="date" name="to" value="{{ $to }}" class="text-xs border border-gray-200 rounded-full px-3 py-1 focus:outline-none focus:ring-2 focus:ring-red-200">
            <button type="submit" class="text-xs font-semibold text-gray-700 border border-gray-200 rounded-full px-3 py-1 hover:border-gray-300">Apply</button>
          </form>
          <button type="button" id="exportCsvBtn" data-export-base="{{ route('admin.reports.dashboard.csv', ['period' => 'week']) }}" class="w-9 h-9 rounded-full border border-gray-200 grid place-items-center text-gray-600 hover:border-gray-300" title="Export Excel">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <path d="M14 2v6h6"/>
              <path d="M9 12l6 6M15 12l-6 6"/>
            </svg>
          </button>
          <button type="button" id="exportPdfBtn" data-export-base="{{ route('admin.reports.dashboard.pdf', ['period' => 'week']) }}" class="w-9 h-9 rounded-full border border-red-200 grid place-items-center text-red-700 hover:border-red-300" title="Export PDF">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <path d="M14 2v6h6"/>
              <path d="M8 13h4a2 2 0 0 1 0 4H8z"/>
              <path d="M14 17v-4h2a2 2 0 0 1 0 4h-2z"/>
            </svg>
          </button>
        </div>
      </div>
      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left text-xs text-gray-500">
              <th class="py-2">{{ __('admin.name') }}</th>
              <th class="py-2">{{ __('admin.reviews') }}</th>
              <th class="py-2">{{ __('admin.rating') }}</th>
              <th class="py-2 text-right">{{ __('admin.actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($reviews as $review)
              <tr>
                <td class="py-3 font-medium text-gray-900">{{ $review['name'] ?? '-' }}</td>
                <td class="py-3 text-gray-600">{{ \Illuminate\Support\Str::limit($review['text'] ?? '', 60) }}</td>
                <td class="py-3 text-gray-700">{{ $review['rating'] ?? '-' }}</td>
                <td class="py-3 text-right">
                  <a href="{{ $review['url'] ?? '#' }}" class="text-xs font-semibold text-red-700">{{ __('admin.view') }}</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="py-6 text-center text-gray-500">{{ __('admin.no_recent_reviews') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
      <div class="text-sm font-semibold text-gray-900">{{ __('admin.recent_branches') }}</div>
      <div class="mt-4 space-y-4">
        @forelse($branches ?? [] as $br)
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gray-100 border border-gray-100 overflow-hidden flex-shrink-0">
              @if(!empty($br['logo_url']))
                <img src="{{ $br['logo_url'] }}" alt="Logo" class="w-full h-full object-cover">
              @else
                <div class="w-full h-full grid place-items-center text-gray-500 text-xs">
                  {{ strtoupper(mb_substr($br['name'] ?? 'B', 0, 1)) }}
                </div>
              @endif
            </div>
            <div class="min-w-0">
              <div class="text-sm font-semibold text-gray-900 truncate">{{ $br['name'] ?? '-' }}</div>
              <div class="text-xs text-gray-500 truncate">{{ $br['brand'] ?? '' }}</div>
            </div>
            @if(!empty($br['cover_url']))
              <div class="ml-auto w-16 h-10 rounded-xl overflow-hidden border border-gray-100">
                <img src="{{ $br['cover_url'] }}" alt="Cover" class="w-full h-full object-cover">
              </div>
            @endif
          </div>
        @empty
          <div class="text-sm text-gray-500">{{ __('admin.no_recent_branches') }}</div>
        @endforelse
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    (function(){
      document.querySelectorAll('.js-bar[data-width]').forEach(function(el){
        const w = parseInt(el.getAttribute('data-width'), 10);
        if (!Number.isFinite(w)) return;
        el.style.width = w + '%';
      });

      const chartEl = document.getElementById('reviewsChart');
      if (!chartEl) return;

      let values = [];
      try {
        values = JSON.parse(chartEl.getAttribute('data-values') || '[]');
      } catch (e) {
        values = [];
      }
      if (!Array.isArray(values) || values.length < 2) return;

      const svg = chartEl.querySelector('svg');
      const area = chartEl.querySelector('#reviewsArea');
      const line = chartEl.querySelector('#reviewsLine');
      const dots = chartEl.querySelector('#reviewsDots');
      const noData = document.getElementById('reviewsNoData');
      if (!svg || !area || !line || !dots) return;

      const width = 640;
      const height = 200;
      const pad = 18;

      values = values.map(v => Number(v) || 0);
      const max = Math.max.apply(null, values.concat([1]));
      const min = Math.min.apply(null, values);
      const range = Math.max(1, max - min);
      const step = (width - pad * 2) / (values.length - 1);

      function pointY(value){
        const t = (value - min) / range;
        return (height - pad) - t * (height - pad * 2);
      }

      const points = values.map(function(v, i){
        const x = pad + step * i;
        const y = pointY(v);
        return [x, y];
      });

      const linePath = points.map(function(p, i){
        return (i === 0 ? 'M' : 'L') + p[0].toFixed(2) + ' ' + p[1].toFixed(2);
      }).join(' ');

      const areaPath = linePath
        + ' L ' + (pad + step * (values.length - 1)).toFixed(2) + ' ' + (height - pad).toFixed(2)
        + ' L ' + pad.toFixed(2) + ' ' + (height - pad).toFixed(2)
        + ' Z';

      line.setAttribute('d', linePath);
      area.setAttribute('d', areaPath);

      dots.innerHTML = '';
      points.forEach(function(p){
        const c = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        c.setAttribute('cx', p[0].toFixed(2));
        c.setAttribute('cy', p[1].toFixed(2));
        c.setAttribute('r', '4');
        c.setAttribute('fill', '#b91c1c');
        c.setAttribute('stroke', '#fff');
        c.setAttribute('stroke-width', '2');
        dots.appendChild(c);
      });

      const allZero = values.every(v => v === 0);
      if (noData) noData.classList.toggle('hidden', !allZero);

      const labelGrid = document.querySelector('[data-chart-cols]');
      if (labelGrid) {
        const cols = parseInt(labelGrid.getAttribute('data-chart-cols') || '0', 10);
        if (cols > 0) {
          labelGrid.style.gridTemplateColumns = `repeat(${cols}, minmax(0, 1fr))`;
        }
      }

      const csvBtn = document.getElementById('exportCsvBtn');
      const pdfBtn = document.getElementById('exportPdfBtn');
      function buildExportUrl(base){
        const from = document.querySelector('input[name="from"]')?.value || '';
        const to = document.querySelector('input[name="to"]')?.value || '';
        const params = new URLSearchParams();
        if (from) params.set('from', from);
        if (to) params.set('to', to);
        const qs = params.toString();
        return qs ? (base + '?' + qs) : base;
      }
      csvBtn?.addEventListener('click', function(){
        const base = csvBtn.getAttribute('data-export-base') || '';
        if (!base) return;
        const url = buildExportUrl(base);
        window.location.href = url;
      });
      pdfBtn?.addEventListener('click', function(){
        const base = pdfBtn.getAttribute('data-export-base') || '';
        if (!base) return;
        const url = buildExportUrl(base);
        window.location.href = url;
      });
    })();
  </script>
@endpush












