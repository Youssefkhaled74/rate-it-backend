@extends('vendor.layouts.app')

@section('title', __('vendor.voucher_history'))

@section('content')
  <div class="flex items-start justify-between gap-4 mb-6">
    <div>
      <div class="text-xs uppercase tracking-wide text-gray-400">{{ __('vendor.voucher_history') }}</div>
      <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ __('vendor.voucher_history') }}</div>
      <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('vendor.vouchers_used') }}</div>
    </div>
    <a href="{{ route('vendor.vouchers.verify') }}" class="px-4 py-2 rounded-xl bg-red-700 text-white text-sm font-semibold shadow-sm hover:bg-red-800">
      {{ __('vendor.verify_voucher') }}
    </a>
  </div>

  <form method="GET" class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow mb-5 border border-gray-100 dark:border-slate-800">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
      @if(($vendor->role ?? '') === 'VENDOR_ADMIN')
        <div>
          <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.branch') }}</label>
          <select name="branch_id" class="w-full border rounded-xl px-3 py-2">
            <option value="">{{ __('vendor.all') }}</option>
            @foreach($branches as $b)
              <option value="{{ $b->id }}" @selected(($filters['branch_id'] ?? '') == $b->id)>{{ $b->name }}</option>
            @endforeach
          </select>
        </div>
      @endif
      <div>
        <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.status') }}</label>
        <select name="status" class="w-full border rounded-xl px-3 py-2">
          <option value="">{{ __('vendor.all') }}</option>
          <option value="USED" @selected(($filters['status'] ?? '') === 'USED')>{{ __('vendor.used') }}</option>
          <option value="EXPIRED" @selected(($filters['status'] ?? '') === 'EXPIRED')>{{ __('vendor.expired') }}</option>
        </select>
      </div>
      <div>
        <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.date_from') }}</label>
        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full border rounded-xl px-3 py-2">
      </div>
      <div>
        <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('vendor.date_to') }}</label>
        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full border rounded-xl px-3 py-2">
      </div>
    </div>
    <div class="mt-4 flex items-center gap-2">
      <button class="px-4 py-2 rounded-xl bg-red-700 text-white text-sm font-semibold">{{ __('vendor.filter') }}</button>
      <a href="{{ route('vendor.vouchers.history') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 dark:text-gray-300 text-sm font-semibold">{{ __('vendor.all') }}</a>
    </div>
  </form>

  <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow border border-gray-100 dark:border-slate-800">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-xs text-gray-500 dark:text-gray-400">
          <tr class="border-b">
            <th class="text-left py-3">{{ __('vendor.code') }}</th>
            <th class="text-left py-3">{{ __('vendor.status') }}</th>
            <th class="text-left py-3">{{ __('vendor.used_at') }}</th>
            <th class="text-left py-3">{{ __('vendor.used_branch') }}</th>
            <th class="text-left py-3">{{ __('vendor.verified_by') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($history as $item)
            <tr class="border-b">
              <td class="py-3 font-semibold">{{ $item->code }}</td>
              <td class="py-3">
                <span class="text-xs px-2 py-1 rounded-full {{ $item->status === 'USED' ? 'bg-green-100 text-green-700' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-300' }}">
                  {{ $item->status }}
                </span>
              </td>
              <td class="py-3">{{ $item->used_at?->format('Y-m-d H:i') ?? '-' }}</td>
              <td class="py-3">{{ $item->usedBranch?->name ?? '-' }}</td>
              <td class="py-3">{{ $item->verifiedByVendor?->name ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-10">
                <div class="flex flex-col items-center text-center text-gray-500 dark:text-gray-400">
                  <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center text-red-600 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                      <path d="M6 2h12v20l-3-2-3 2-3-2-3 2z"/>
                      <path d="M9 6h6"/>
                      <path d="M9 10h6"/>
                      <path d="M9 14h6"/>
                    </svg>
                  </div>
                  <div class="text-sm font-semibold">{{ __('vendor.no_data') }}</div>
                  <div class="text-xs mt-1">Try verifying a voucher to see history here.</div>
                  <a href="{{ route('vendor.vouchers.verify') }}" class="mt-3 px-4 py-2 rounded-xl bg-red-700 text-white text-xs font-semibold">{{ __('vendor.verify_voucher') }}</a>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $history->appends(request()->query())->links() }}
    </div>
  </div>
@endsection


