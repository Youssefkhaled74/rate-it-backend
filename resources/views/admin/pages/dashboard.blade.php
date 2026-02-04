@extends('admin.layouts.app')

@section('page_title', __('admin.dashboard'))
@section('title', __('admin.dashboard_title'))

@section('content')
  {{-- Hero --}}
  <div class="bg-white rounded-[28px] shadow-soft p-6 border border-gray-100 relative overflow-hidden">
    <div class="flex items-center justify-between gap-6">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-gray-100 overflow-hidden">
          <img src="{{ asset('assets/images/userdefultphoto.png') }}" alt="avatar" class="w-12 h-12 object-cover">
        </div>
        <div>
          <div class="text-xs text-gray-500">{{ __('admin.good_morning') }}</div>
          <div class="text-base font-semibold text-gray-900">{{ $welcomeName ?? __('admin.admin') }}</div>
          <div class="text-sm text-gray-600 mt-1">
            {{ __('admin.dashboard_headline', ['count' => ($counts['all'] ?? 0)]) }}
          </div>
        </div>
      </div>

      <div class="hidden md:block">
        <div class="w-36 h-20 rounded-3xl bg-red-50 border border-red-100 grid place-items-center">
          <span class="text-red-300 text-4xl">✦</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Quick Stats --}}
  <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft flex items-center justify-between">
      <div class="text-sm text-gray-600">{{ __('admin.total_users') }}</div>
      <div class="text-2xl font-semibold text-gray-900">{{ $stats['total_users'] ?? 0 }}</div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft flex items-center justify-between">
      <div class="text-sm text-gray-600">{{ __('admin.total_brands') }}</div>
      <div class="text-2xl font-semibold text-gray-900">{{ $stats['total_brands'] ?? 0 }}</div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft flex items-center justify-between">
      <div class="text-sm text-gray-600">{{ __('admin.average_rating') }}</div>
      <div class="text-2xl font-semibold text-gray-900">{{ $stats['average_rating'] ?? '0.0' }}</div>
    </div>
  </div>

  {{-- Charts Row --}}
  <div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
      <div class="flex items-center justify-between">
        <div class="text-sm font-semibold text-gray-900">{{ __('admin.reviews_over_time') }}</div>
        <button class="text-xs text-red-700 font-semibold">{{ __('admin.filter') }}</button>
      </div>
      <div class="mt-4 h-60 rounded-2xl bg-gray-50 border border-gray-100 grid place-items-center text-gray-400">
        {{ __('admin.chart_placeholder') }}
      </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
      <div class="flex items-center justify-between">
        <div class="text-sm font-semibold text-gray-900">{{ __('admin.user_growth') }}</div>
        <div class="text-xs text-gray-400">{{ __('admin.last_14_days') }}</div>
      </div>
      <div class="mt-4 space-y-3">
        @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $m)
          <div class="flex items-center gap-3">
            <div class="w-8 text-xs text-gray-500">{{ $m }}</div>
            <div class="flex-1 h-2 rounded-full bg-gray-100 overflow-hidden">
              <div class="h-2 rounded-full bg-red-800 js-bar" data-width="{{ rand(30, 95) }}"></div>
            </div>
            <div class="text-[10px] text-gray-400">( {{ rand(120, 900) }} )</div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Bottom Row --}}
  <div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
      <div class="text-sm font-semibold text-gray-900">{{ __('admin.recent_reviews_moderation') }}</div>
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
                  <a href="#" class="text-xs font-semibold text-red-700">{{ __('admin.view') }}</a>
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
    })();
  </script>
@endpush












