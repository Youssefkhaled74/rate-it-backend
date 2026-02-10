@extends('admin.layouts.app')

@section('page_title', __('admin.vouchers'))
@section('title', __('admin.vouchers'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.vouchers') }}</h2>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.vouchers_hint') }}</div>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('admin.vouchers.export.csv', request()->query()) }}"
           class="rounded-full border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
          {{ __('admin.export_csv') }}
        </a>
      </div>
    </div>

    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.vouchers_total') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.vouchers_valid') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['valid'] ?? 0 }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.vouchers_used') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['used'] ?? 0 }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.vouchers_expired') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['expired'] ?? 0 }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.points_used') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['points_used'] ?? 0 }}</div>
      </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3">
      <input name="q" value="{{ $q }}" placeholder="{{ __('admin.search_by_code_or_user') }}"
             class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      <select name="status" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <option value="">{{ __('admin.all') }}</option>
        <option value="VALID" {{ $status === 'VALID' ? 'selected' : '' }}>{{ __('admin.status_valid') }}</option>
        <option value="USED" {{ $status === 'USED' ? 'selected' : '' }}>{{ __('admin.status_used') }}</option>
        <option value="EXPIRED" {{ $status === 'EXPIRED' ? 'selected' : '' }}>{{ __('admin.status_expired') }}</option>
      </select>
      <select name="brand_id" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <option value="">{{ __('admin.all_brands') }}</option>
        @foreach($brands as $b)
          <option value="{{ $b->id }}" {{ (int)$brandId === (int)$b->id ? 'selected' : '' }}>
            {{ $b->name_en ?? $b->name_ar ?? ('#'.$b->id) }}
          </option>
        @endforeach
      </select>
      <input type="date" name="from" value="{{ $from }}"
             class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      <input type="date" name="to" value="{{ $to }}"
             class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      <div class="flex items-center gap-2">
        <button type="submit" class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">
          {{ __('admin.filter') }}
        </button>
        <a href="{{ route('admin.vouchers.index') }}" class="text-sm text-gray-500">{{ __('admin.reset') }}</a>
      </div>
    </form>
  </div>

  <div class="mt-5 bg-white border border-gray-100 rounded-[24px] p-4 shadow-soft">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-xs uppercase text-gray-400 border-b">
          <tr>
            <th class="text-left py-3 px-2">{{ __('admin.voucher_code') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.status') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.user') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.brand') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.points_used') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.value') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.issued_at') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.used_at') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.expires_at') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.used_branch') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($vouchers as $v)
            <tr>
              <td class="py-3 px-2 font-semibold text-gray-900">{{ $v->code }}</td>
              <td class="py-3 px-2">
                @if($v->status === 'VALID')
                  <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">{{ __('admin.status_valid') }}</span>
                @elseif($v->status === 'USED')
                  <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">{{ __('admin.status_used') }}</span>
                @else
                  <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">{{ __('admin.status_expired') }}</span>
                @endif
              </td>
              <td class="py-3 px-2 text-gray-700">
                <div class="font-medium">{{ $v->user?->name ?? '-' }}</div>
                <div class="text-xs text-gray-500">{{ $v->user?->phone ?? '-' }}</div>
              </td>
              <td class="py-3 px-2 text-gray-700">{{ $v->brand?->name_en ?? $v->brand?->name_ar ?? '-' }}</td>
              <td class="py-3 px-2 text-gray-700">{{ $v->points_used }}</td>
              <td class="py-3 px-2 text-gray-700">{{ $v->value_amount ?? 0 }}</td>
              <td class="py-3 px-2 text-gray-600">{{ $v->issued_at?->format('Y-m-d H:i') ?? '-' }}</td>
              <td class="py-3 px-2 text-gray-600">{{ $v->used_at?->format('Y-m-d H:i') ?? '-' }}</td>
              <td class="py-3 px-2 text-gray-600">{{ $v->expires_at?->format('Y-m-d H:i') ?? '-' }}</td>
              <td class="py-3 px-2 text-gray-600">{{ $v->usedBranch?->name ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="py-6 text-center text-gray-500">{{ __('admin.no_vouchers') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-6">
    {{ $vouchers->links() }}
  </div>
@endsection
