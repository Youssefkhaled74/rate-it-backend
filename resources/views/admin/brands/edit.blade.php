@extends('admin.layouts.app')

@section('title', __('admin.edit_brand'))

@section('content')
<div class="max-w-6xl space-y-6">
  <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">

    <div class="px-6 md:px-8 py-6 border-b border-gray-100 bg-gradient-to-br from-white to-gray-50">
      <div class="flex items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">{{ __('admin.edit_brand') }}</h2>
          <p class="text-sm text-gray-500 mt-1">{{ __('admin.edit_brand_hint') }}</p>
        </div>
        <a href="{{ route('admin.brands.index') }}"
           class="rounded-2xl bg-white border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
          {{ __('admin.back') }}
        </a>
      </div>
    </div>

    @if ($errors->any())
      <div class="mx-6 md:mx-8 mt-6 rounded-2xl bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3">
        <div class="font-semibold mb-1">{{ __('admin.fix_errors') }}</div>
        <ul class="list-disc pl-5 space-y-1">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.brands.update', $brand) }}" enctype="multipart/form-data" class="px-6 md:px-8 py-6">
      @csrf
      @method('PUT')
      @include('admin.brands._form', ['brand' => $brand])

      <div class="mt-8 flex flex-col items-center gap-3">
        <button class="w-full max-w-md rounded-2xl bg-red-900 text-white py-3.5 text-sm font-semibold shadow-lg shadow-red-900/20 hover:bg-red-950 transition">
          {{ __('admin.save') }}
        </button>
        <a href="{{ route('admin.brands.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
          {{ __('admin.cancel') }}
        </a>
      </div>
    </form>
  </div>

  <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">
    <div class="px-6 md:px-8 py-6 border-b border-gray-100">
      <div class="flex items-center justify-between gap-4">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.branches') }}</h3>
          <p class="text-sm text-gray-500 mt-1">{{ __('admin.branches_hint') }}</p>
        </div>
        <a href="{{ route('admin.branches.create', ['brand_id' => $brand->id]) }}"
           class="rounded-2xl bg-red-900 text-white px-4 py-2.5 text-sm font-semibold hover:bg-red-950 transition">
          {{ __('admin.add_branch') }}
        </a>
      </div>
    </div>

    <div class="p-6 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-xs text-gray-500">
            <th class="py-2">{{ __('admin.branch_name') }}</th>
            <th class="py-2">{{ __('admin.city') }}</th>
            <th class="py-2">{{ __('admin.address') }}</th>
            <th class="py-2">{{ __('admin.average_rating') }}</th>
            <th class="py-2">{{ __('admin.password') }}</th>
            <th class="py-2">{{ __('admin.qr_code') }}</th>
            <th class="py-2 text-right">{{ __('admin.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @php
            $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
            $emojiForRating = function ($avg) {
              if ($avg >= 4.5) return '😍';
              if ($avg >= 4.0) return '😊';
              if ($avg >= 3.0) return '🙂';
              if ($avg >= 2.0) return '😕';
              return '😞';
            };
          @endphp
          @forelse($branches ?? [] as $br)
            @php
              $cityName = $br->city?->{'name_' . $locale} ?? $br->city?->name_en ?? '-';
              $avg = $br->reviews_avg_overall_rating ?? null;
            @endphp
            <tr>
              <td class="py-4">
                <div class="font-medium text-gray-900">{{ $br->name ?: ($br->place?->display_name ?? '-') }}</div>
                <div class="text-xs text-gray-400">{{ $brand->name_en ?? $brand->name_ar ?? '' }}</div>
              </td>
              <td class="py-4 text-gray-600">{{ $cityName }}</td>
              <td class="py-4 text-gray-600">{{ $br->address ?? '-' }}</td>
              <td class="py-4">
                @if($avg !== null)
                  <span class="inline-flex items-center gap-2 text-gray-700">
                    <span>{{ $emojiForRating((float) $avg) }}</span>
                    <span>({{ number_format((float) $avg, 1) }})</span>
                  </span>
                @else
                  <span class="text-gray-400">-</span>
                @endif
              </td>
              <td class="py-4 text-gray-400">••••••••</td>
              <td class="py-4">
                <a href="{{ route('admin.branches.qr', $br) }}" target="_blank"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-green-50 text-green-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="5" />
                    <circle cx="12" cy="12" r="2" />
                  </svg>
                </a>
              </td>
              <td class="py-4 text-right">
                <div class="inline-flex items-center gap-2">
                  <a href="{{ route('admin.branches.edit', $br) }}"
                     class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-amber-50 text-amber-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                      <path d="M12 20h9"/>
                      <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                    </svg>
                  </a>
                  <a href="{{ route('admin.branches.show', $br) }}"
                     class="inline-flex items-center justify-center px-4 h-9 rounded-full bg-gray-100 text-red-700 text-xs font-semibold">
                    {{ __('admin.view') }}
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-8 text-center text-gray-500">{{ __('admin.no_recent_branches') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">
    <div class="px-6 md:px-8 py-6 border-b border-gray-100 bg-gradient-to-br from-white to-gray-50">
      <div class="flex items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">{{ __('admin.brand_admin') }}</h2>
          <p class="text-sm text-gray-500 mt-1">{{ __('admin.brand_admin_hint') }}</p>
        </div>
        @if(!empty($vendorAdmin))
          <span class="text-xs font-semibold rounded-full px-3 py-1 bg-green-50 text-green-700 border border-green-100">{{ __('admin.assigned') }}</span>
        @else
          <span class="text-xs font-semibold rounded-full px-3 py-1 bg-yellow-50 text-yellow-700 border border-yellow-100">{{ __('admin.not_assigned') }}</span>
        @endif
      </div>
    </div>

    <form method="POST" action="{{ route('admin.brands.vendor-admin.save', $brand) }}" class="px-6 md:px-8 py-6">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <div class="rounded-2xl border border-rose-100 bg-rose-50/40 px-4 py-3 text-xs text-rose-700">
            {{ $vendorAdmin ? __('admin.brand_admin_keep_password') : __('admin.brand_admin_set_password') }}
          </div>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700">{{ __('admin.name') }}</label>
          <input type="text" name="name" value="{{ old('name', $vendorAdmin->name ?? '') }}"
                 class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700">{{ __('admin.phone') }}</label>
          <input type="text" name="phone" value="{{ old('phone', $vendorAdmin->phone ?? '') }}"
                 class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700">{{ __('admin.email') }}</label>
          <input type="email" name="email" value="{{ old('email', $vendorAdmin->email ?? '') }}"
                 class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700">{{ __('admin.password') }}</label>
          <input type="password" name="password"
                 placeholder="{{ $vendorAdmin ? __('admin.leave_blank_to_keep') : '' }}"
                 class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-semibold text-gray-700">{{ __('admin.confirm_password') }}</label>
          <input type="password" name="password_confirmation"
                 class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        </div>
      </div>

      <div class="mt-6 flex flex-col sm:flex-row gap-3">
        <button class="sm:flex-1 rounded-2xl bg-red-900 text-white px-6 py-3 text-sm font-semibold shadow-lg shadow-red-900/20 hover:bg-red-950 transition">
          {{ $vendorAdmin ? __('admin.update_brand_admin') : __('admin.create_brand_admin') }}
        </button>
        <a href="{{ route('admin.brands.index') }}"
           class="sm:flex-1 text-center rounded-2xl bg-white border border-gray-200 px-6 py-3 text-sm font-semibold text-gray-800 hover:bg-gray-50 transition">
          {{ __('admin.back_to_brands') }}
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
