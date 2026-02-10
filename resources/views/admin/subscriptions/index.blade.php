@extends('admin.layouts.app')

@section('page_title', __('admin.subscriptions'))
@section('title', __('admin.subscriptions'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.subscriptions') }}</h2>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.subscriptions_hint') }}</div>
      </div>
    </div>

    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.total') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.free') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['free'] ?? 0 }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.active') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['active'] ?? 0 }}</div>
      </div>
      <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
        <div class="text-xs text-gray-500">{{ __('admin.expired') }}</div>
        <div class="text-lg font-semibold text-gray-900">{{ $stats['expired'] ?? 0 }}</div>
      </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3">
      <input name="q" value="{{ $q }}" placeholder="{{ __('admin.search_by_user') }}"
             class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      <select name="status" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <option value="">{{ __('admin.all') }}</option>
        <option value="FREE" {{ $status === 'FREE' ? 'selected' : '' }}>{{ __('admin.free') }}</option>
        <option value="ACTIVE" {{ $status === 'ACTIVE' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
        <option value="EXPIRED" {{ $status === 'EXPIRED' ? 'selected' : '' }}>{{ __('admin.expired') }}</option>
      </select>
      <select name="plan_id" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <option value="">{{ __('admin.all_plans') }}</option>
        @foreach($plans as $p)
          <option value="{{ $p->id }}" {{ (int)$planId === (int)$p->id ? 'selected' : '' }}>
            {{ $p->name_en ?? $p->name_ar ?? $p->code }}
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
        <a href="{{ route('admin.subscriptions.index') }}" class="text-sm text-gray-500">{{ __('admin.reset') }}</a>
      </div>
    </form>
  </div>

  <div class="mt-5 bg-white border border-gray-100 rounded-[24px] p-4 shadow-soft">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-xs uppercase text-gray-400 border-b">
          <tr>
            <th class="text-left py-3 px-2">{{ __('admin.user') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.subscription_plan') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.status') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.free_until') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.paid_until') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.started_at') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($subs as $s)
            <tr>
              <td class="py-3 px-2 text-gray-700">
                <div class="font-semibold text-gray-900">{{ $s->user?->name ?? '-' }}</div>
                <div class="text-xs text-gray-500">{{ $s->user?->phone ?? $s->user?->email ?? '-' }}</div>
              </td>
              <td class="py-3 px-2 text-gray-700">{{ $s->plan?->name_en ?? $s->plan?->name_ar ?? '-' }}</td>
              <td class="py-3 px-2">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                  {{ $s->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700' : ($s->status === 'FREE' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                  {{ $s->status }}
                </span>
              </td>
              <td class="py-3 px-2 text-gray-600">{{ $s->free_until?->format('Y-m-d') ?? '-' }}</td>
              <td class="py-3 px-2 text-gray-600">{{ $s->paid_until?->format('Y-m-d') ?? '-' }}</td>
              <td class="py-3 px-2 text-gray-600">{{ $s->started_at?->format('Y-m-d') ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-6 text-center text-gray-500">{{ __('admin.no_subscriptions') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-6">
    {{ $subs->links() }}
  </div>
@endsection
