@extends('admin.layouts.app')

@section('page_title', __('admin.subscription_plans'))
@section('title', __('admin.subscription_plans'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.subscription_plans') }}</h2>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.subscription_plans_hint') }}</div>
      </div>
      <a href="{{ route('admin.subscription-plans.create') }}"
         class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">
        {{ __('admin.add_plan') }}
      </a>
    </div>

    @if(session('success'))
      <div class="mt-4 rounded-2xl bg-green-50 border border-green-100 text-green-700 text-sm px-4 py-3">
        {{ session('success') }}
      </div>
    @endif

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
      @endphp
      <div class="bg-white border border-gray-100 rounded-[24px] p-5 shadow-soft">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <div class="text-sm text-gray-500">{{ $p->code }}</div>
            <div class="text-lg font-semibold text-gray-900 truncate">{{ $p->name_en }}</div>
            <div class="text-xs text-gray-500 mt-1 truncate">{{ $p->name_ar ?? '-' }}</div>
            <div class="text-sm text-gray-700 mt-2">
              {{ $price }} {{ $p->currency }} • {{ $p->interval_count }} {{ $p->interval === 'year' ? __('admin.year') : __('admin.month') }}
            </div>
            <div class="text-xs text-gray-500 mt-1">
              {{ __('admin.trial_days') }}: {{ $p->trial_days ?? 0 }}
            </div>
          </div>

          <div class="flex items-center gap-2">
            @if($p->is_best_value)
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700">{{ __('admin.best_value') }}</span>
            @endif
            <form method="POST" action="{{ route('admin.subscription-plans.toggle', $p) }}">
              @csrf
              @method('PATCH')
              <button type="submit"
                class="w-10 h-6 rounded-full {{ $p->is_active ? 'bg-green-500' : 'bg-gray-200' }} relative transition">
                <span class="absolute top-0.5 {{ $p->is_active ? 'left-5' : 'left-0.5' }} w-5 h-5 rounded-full bg-white transition"></span>
              </button>
            </form>
            <a href="{{ route('admin.subscription-plans.edit', $p) }}"
               class="w-8 h-8 rounded-full bg-white border border-gray-200 grid place-items-center text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21 7.5a2.5 2.5 0 0 0-2.5-2.5H7A2.5 2.5 0 0 0 4.5 7.5v9A2.5 2.5 0 0 0 7 19h10.5a2.5 2.5 0 0 0 2.5-2.5v-9Z"/>
              </svg>
            </a>
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
