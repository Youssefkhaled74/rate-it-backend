@extends('admin.layouts.app')

@section('title', __('admin.lookups'))

@section('content')
<div class="space-y-6">
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
      <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.lookups') }}</h2>
      <p class="text-sm text-gray-500 mt-1">{{ __('admin.lookups_subtitle') }}</p>
    </div>
  </div>

  {{-- Hero / Quick overview --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 rounded-[26px] bg-white border border-gray-100 shadow-soft p-6 relative overflow-hidden">
      <div class="absolute -right-12 -top-12 w-40 h-40 rounded-full bg-red-50"></div>
      <div class="absolute right-8 top-6 w-16 h-16 rounded-2xl bg-red-50 border border-red-100 grid place-items-center text-red-900">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M12 3v4"/><path d="M12 17v4"/><path d="M3 12h4"/><path d="M17 12h4"/><circle cx="12" cy="12" r="3"/>
        </svg>
      </div>
      <div class="relative">
        <div class="text-sm text-gray-500">{{ __('admin.lookups') }}</div>
        <div class="text-lg font-semibold text-gray-900 mt-1">{{ __('admin.lookups_headline') }}</div>
        <div class="text-sm text-gray-600 mt-2 max-w-xl">{{ __('admin.lookups_help') }}</div>
      </div>
    </div>

    <div class="rounded-[26px] bg-white border border-gray-100 shadow-soft p-6">
      <div class="text-sm font-semibold text-gray-900">{{ __('admin.quick_access') }}</div>
      <div class="mt-4 flex flex-wrap gap-2">
        <span class="px-3 py-1 rounded-full bg-red-50 text-red-800 text-xs font-semibold">{{ __('admin.genders') }}</span>
        <span class="px-3 py-1 rounded-full bg-red-50 text-red-800 text-xs font-semibold">{{ __('admin.nationalities') }}</span>
        <span class="px-3 py-1 rounded-full bg-red-50 text-red-800 text-xs font-semibold">{{ __('admin.cities') }}</span>
        <span class="px-3 py-1 rounded-full bg-red-50 text-red-800 text-xs font-semibold">{{ __('admin.areas') }}</span>
      </div>
      <div class="mt-5 text-xs text-gray-500">{{ __('admin.lookups_tip') }}</div>
    </div>
  </div>

  {{-- Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <a href="{{ route('admin.lookups.genders.index', ['lang' => request('lang')]) }}"
       class="group rounded-[24px] bg-white border border-gray-100 shadow-soft p-6 hover:border-gray-200 hover:shadow-md transition">
      <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 grid place-items-center text-red-900 group-hover:scale-105 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M12 15a7 7 0 1 0-7-7 7 7 0 0 0 7 7z" />
          <path d="M12 15v7" />
          <path d="M9 19h6" />
        </svg>
      </div>
      <div class="mt-4 text-sm font-semibold text-gray-900">{{ __('admin.genders') }}</div>
      <div class="text-xs text-gray-500 mt-1">{{ __('admin.manage_genders') }}</div>
      <div class="mt-4 text-xs text-red-800 font-semibold inline-flex items-center gap-2">
        {{ __('admin.manage') }}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 18l6-6-6-6"/>
        </svg>
      </div>
    </a>

    <a href="{{ route('admin.lookups.nationalities.index', ['lang' => request('lang')]) }}"
       class="group rounded-[24px] bg-white border border-gray-100 shadow-soft p-6 hover:border-gray-200 hover:shadow-md transition">
      <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 grid place-items-center text-red-900 group-hover:scale-105 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="12" cy="12" r="10" />
          <path d="M2 12h20" />
          <path d="M12 2a15.3 15.3 0 0 1 0 20" />
          <path d="M12 2a15.3 15.3 0 0 0 0 20" />
        </svg>
      </div>
      <div class="mt-4 text-sm font-semibold text-gray-900">{{ __('admin.nationalities') }}</div>
      <div class="text-xs text-gray-500 mt-1">{{ __('admin.manage_nationalities') }}</div>
      <div class="mt-4 text-xs text-red-800 font-semibold inline-flex items-center gap-2">
        {{ __('admin.manage') }}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 18l6-6-6-6"/>
        </svg>
      </div>
    </a>

    <a href="{{ route('admin.lookups.cities.index', ['lang' => request('lang')]) }}"
       class="group rounded-[24px] bg-white border border-gray-100 shadow-soft p-6 hover:border-gray-200 hover:shadow-md transition">
      <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 grid place-items-center text-red-900 group-hover:scale-105 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M3 21h18" />
          <path d="M4 21V8l8-5 8 5v13" />
          <path d="M9 21v-6h6v6" />
        </svg>
      </div>
      <div class="mt-4 text-sm font-semibold text-gray-900">{{ __('admin.cities') }}</div>
      <div class="text-xs text-gray-500 mt-1">{{ __('admin.manage_cities') }}</div>
      <div class="mt-4 text-xs text-red-800 font-semibold inline-flex items-center gap-2">
        {{ __('admin.manage') }}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 18l6-6-6-6"/>
        </svg>
      </div>
    </a>

    <a href="{{ route('admin.lookups.areas.index', ['lang' => request('lang')]) }}"
       class="group rounded-[24px] bg-white border border-gray-100 shadow-soft p-6 hover:border-gray-200 hover:shadow-md transition">
      <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 grid place-items-center text-red-900 group-hover:scale-105 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M12 21s-7-4.6-7-11a7 7 0 0 1 14 0c0 6.4-7 11-7 11z" />
          <circle cx="12" cy="10" r="3" />
        </svg>
      </div>
      <div class="mt-4 text-sm font-semibold text-gray-900">{{ __('admin.areas') }}</div>
      <div class="text-xs text-gray-500 mt-1">{{ __('admin.manage_areas') }}</div>
      <div class="mt-4 text-xs text-red-800 font-semibold inline-flex items-center gap-2">
        {{ __('admin.manage') }}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 18l6-6-6-6"/>
        </svg>
      </div>
    </a>
  </div>
</div>
@endsection
