@extends('admin.layouts.app')

@section('title', __('admin.banners_onboarding'))

@section('content')
<div class="space-y-8">

  {{-- Header row --}}
  <div class="flex items-center justify-between">
    <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.banners_onboarding') }}</h2>
  </div>

  {{-- Banners --}}
  <div class="space-y-4">
    <div class="text-sm font-semibold text-gray-900">{{ __('admin.banners') }}</div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <a href="{{ route('admin.banners.create') }}"
         class="group rounded-[22px] border-2 border-dashed border-gray-200 bg-white hover:border-gray-300 transition shadow-soft p-6 flex flex-col items-center justify-center min-h-[200px]">
        <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 grid place-items-center text-gray-700 group-hover:scale-105 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"/>
          </svg>
        </div>
        <div class="mt-3 text-sm font-semibold text-gray-900">{{ __('admin.add_banner') }}</div>
      </a>

      @foreach($banners as $b)
        @php
          $img = $b->image ? asset($b->image) : asset('assets/images/category-placeholder.png');
        @endphp
        <div class="rounded-[22px] bg-white border border-gray-100 shadow-soft overflow-hidden">
          <div class="relative h-32 bg-gray-100">
            <img src="{{ $img }}" alt="{{ $b->offer_name }}" class="w-full h-full object-cover">

            <form method="POST" action="{{ route('admin.banners.toggle', $b) }}" class="absolute top-3 left-3 rtl-toggle">
              @csrf
              @method('PATCH')
              <button type="submit"
                class="w-10 h-6 rounded-full {{ $b->is_active ? 'bg-red-900' : 'bg-gray-200' }} relative transition">
                <span class="absolute top-0.5 {{ $b->is_active ? 'left-5' : 'left-0.5' }} w-5 h-5 rounded-full bg-white transition"></span>
              </button>
            </form>

            <div class="absolute top-3 right-3 rtl-dots flex items-center gap-2">
              <a href="{{ route('admin.banners.edit', $b) }}"
                 class="w-8 h-8 rounded-full bg-white/95 border border-gray-100 grid place-items-center text-gray-700 hover:bg-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M21 7.5a2.5 2.5 0 0 0-2.5-2.5H7A2.5 2.5 0 0 0 4.5 7.5v9A2.5 2.5 0 0 0 7 19h10.5a2.5 2.5 0 0 0 2.5-2.5v-9Z"/>
                </svg>
              </a>
              <form method="POST" action="{{ route('admin.banners.destroy', $b) }}">
                @csrf
                @method('DELETE')
                <button type="button" data-confirm="delete-banner-{{ $b->id }}" data-confirm-text="{{ __('admin.delete') }}" data-title="{{ __('admin.delete') }}" data-message="{{ __('admin.confirm_message') }}"
                        class="w-8 h-8 rounded-full bg-white/95 border border-gray-100 grid place-items-center text-red-700 hover:bg-white">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 3h6l1 2h4v2H4V5h4l1-2Zm1 6h2v8h-2V9Zm4 0h2v8h-2V9ZM7 9h2v8H7V9Z"/>
                  </svg>
                </button>
                <input type="hidden" name="_confirm_target" value="delete-banner-{{ $b->id }}" />
              </form>
            </div>
          </div>

          <div class="p-4">
            <div class="text-sm font-semibold text-gray-900 truncate">{{ $b->offer_name }}</div>
            <div class="text-[11px] text-gray-500 mt-1 truncate">
              {{ optional($b->start_date)->format('d-m-Y') ?? '-' }}
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Onboarding --}}
  <div class="space-y-4">
    <div class="text-sm font-semibold text-gray-900">{{ __('admin.onboarding') }}</div>

    <div class="space-y-4">
      @foreach($onboardings as $o)
        @php
          $img = $o->image ? asset($o->image) : asset('assets/images/category-placeholder.png');
        @endphp
        <div class="rounded-[22px] bg-white border border-gray-100 shadow-soft p-4 flex items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl overflow-hidden bg-gray-100">
              <img src="{{ $img }}" alt="{{ $o->title }}" class="w-full h-full object-cover">
            </div>
            <div>
              <div class="text-sm font-semibold text-gray-900">{{ $o->title }}</div>
              <div class="text-xs text-gray-500 mt-1">{{ $o->subtitle ?: '-' }}</div>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <a href="{{ route('admin.onboardings.edit', $o) }}"
               class="w-8 h-8 rounded-full bg-white border border-gray-200 grid place-items-center text-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21 7.5a2.5 2.5 0 0 0-2.5-2.5H7A2.5 2.5 0 0 0 4.5 7.5v9A2.5 2.5 0 0 0 7 19h10.5a2.5 2.5 0 0 0 2.5-2.5v-9Z"/>
              </svg>
            </a>
            <form method="POST" action="{{ route('admin.onboardings.destroy', $o) }}">
              @csrf
              @method('DELETE')
              <button type="button" data-confirm="delete-onboarding-{{ $o->id }}" data-confirm-text="{{ __('admin.delete') }}" data-title="{{ __('admin.delete') }}" data-message="{{ __('admin.confirm_message') }}"
                      class="w-8 h-8 rounded-full bg-white border border-gray-200 grid place-items-center text-red-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M9 3h6l1 2h4v2H4V5h4l1-2Zm1 6h2v8h-2V9Zm4 0h2v8h-2V9ZM7 9h2v8H7V9Z"/>
                </svg>
              </button>
              <input type="hidden" name="_confirm_target" value="delete-onboarding-{{ $o->id }}" />
            </form>
          </div>
        </div>
      @endforeach

      <a href="{{ route('admin.onboardings.create') }}"
         class="group rounded-[22px] border-2 border-dashed border-gray-200 bg-white hover:border-gray-300 transition shadow-soft p-6 flex flex-col items-center justify-center min-h-[120px]">
        <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 grid place-items-center text-gray-700 group-hover:scale-105 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"/>
          </svg>
        </div>
        <div class="mt-3 text-sm font-semibold text-gray-900">{{ __('admin.add_onboarding') }}</div>
      </a>
    </div>
  </div>
</div>
@endsection
