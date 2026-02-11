@extends('admin.layouts.app')

@section('title', __('admin.categories'))

@section('content')
@php
  $totalCategories = method_exists($categories, 'total') ? $categories->total() : $categories->count();
  $activeCategories = $categories->where('is_active', 1)->count();
  $inactiveCategories = $categories->where('is_active', 0)->count();
@endphp

<div class="space-y-6">

  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.categories') }}</h2>

    <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
      <div class="w-full max-w-md">
        <form method="get" class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 rtl-search-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="11" cy="11" r="7"></circle>
              <path d="M21 21l-4.3-4.3"></path>
            </svg>
          </span>

          <input
            name="q"
            value="{{ request('q') }}"
            placeholder="{{ __('admin.search') }}"
            class="w-full rounded-2xl border border-gray-200 bg-white/80 pl-11 pr-4 py-3 text-sm outline-none rtl-search-input
                   focus:border-red-300 focus:ring-4 focus:ring-red-100 transition"
          >
        </form>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-[22px] bg-red-900 text-white p-5 shadow-soft">
      <div class="text-sm opacity-90">{{ __('admin.total_categories') }}</div>
      <div class="mt-2 text-3xl font-semibold">{{ $totalCategories }}</div>
    </div>
    <div class="rounded-[22px] bg-white p-5 border border-gray-100 shadow-soft">
      <div class="text-sm text-gray-600">{{ __('admin.active_categories') }}</div>
      <div class="mt-2 text-3xl font-semibold text-red-900">{{ $activeCategories }}</div>
    </div>
    <div class="rounded-[22px] bg-white p-5 border border-gray-100 shadow-soft">
      <div class="text-sm text-gray-600">{{ __('admin.inactive_categories') }}</div>
      <div class="mt-2 text-3xl font-semibold text-red-900">{{ $inactiveCategories }}</div>
    </div>
  </div>

  <div class="flex items-center gap-3">
    <a href="{{ route('admin.categories.index') }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold {{ request('status') ? 'bg-white text-gray-600 border border-gray-100' : 'bg-red-900 text-white' }}">
      {{ __('admin.all_categories') }}
    </a>
    <a href="{{ route('admin.categories.index', ['status' => 'active']) }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold {{ request('status') === 'active' ? 'bg-red-900 text-white' : 'bg-white text-gray-600 border border-gray-100' }}">
      {{ __('admin.active') }}
    </a>
    <a href="{{ route('admin.categories.index', ['status' => 'inactive']) }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold {{ request('status') === 'inactive' ? 'bg-red-900 text-white' : 'bg-white text-gray-600 border border-gray-100' }}">
      {{ __('admin.inactive') }}
    </a>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

    <a href="{{ route('admin.categories.create') }}"
       class="group rounded-[26px] border-2 border-dashed border-gray-200 bg-white/60 hover:bg-white
              hover:border-gray-300 transition shadow-soft p-6 flex flex-col items-center justify-center min-h-[220px]">
      <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 grid place-items-center text-gray-700 group-hover:scale-105 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 5v14M5 12h14"/>
        </svg>
      </div>
      <div class="mt-3 text-sm font-semibold text-gray-900">{{ __('admin.add_category') }}</div>
      <div class="text-xs text-gray-500 mt-1">{{ __('admin.create_new_category') }}</div>
    </a>

    @foreach($categories as $c)
      @php
        $logo = $c->logo ? asset($c->logo) : asset('assets/images/category-placeholder.png');
        $icon = $c->icon ? asset($c->icon) : asset('assets/images/category-icon-placeholder.png');
      @endphp

      <div class="rounded-[26px] bg-white border border-gray-100 shadow-soft p-4 min-h-[220px] flex flex-col">
        <div class="flex items-start justify-between gap-3">
          <div class="flex items-center gap-3 min-w-0">
            <div class="w-12 h-12 rounded-2xl overflow-hidden border border-gray-100 bg-gray-50 flex-shrink-0">
              <img src="{{ $logo }}"
                   alt="{{ $c->name_en }}"
                   class="w-full h-full object-cover js-fallback-img"
                   data-fallback="{{ asset('assets/images/category-placeholder.png') }}">
            </div>
            <div class="min-w-0">
              <div class="text-base font-semibold text-gray-900 truncate">{{ $c->name_en }}</div>
              <div class="text-xs text-gray-500 truncate">{{ $c->name_ar ?: '-' }}</div>
            </div>
          </div>

          <div class="relative rtl-dots">
            <button type="button" class="w-9 h-9 rounded-full bg-white border border-gray-100 grid place-items-center text-gray-700 hover:bg-gray-50"
                    onclick="toggleMenu('catmenu-{{ $c->id }}')">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/>
              </svg>
            </button>

            <div id="catmenu-{{ $c->id }}" class="hidden absolute right-0 mt-2 w-44 rounded-2xl bg-white border border-gray-100 shadow-lg overflow-hidden rtl-menu-right z-20">
              <a href="{{ route('admin.categories.show', $c) }}" class="block px-4 py-3 text-sm hover:bg-gray-50">{{ __('admin.show') }}</a>
              <a href="{{ route('admin.categories.edit', $c) }}" class="block px-4 py-3 text-sm hover:bg-gray-50">{{ __('admin.edit') }}</a>
              <form method="POST" action="{{ route('admin.categories.destroy', $c) }}">
                @csrf
                @method('DELETE')
                <button type="button" data-confirm="delete-cat-{{ $c->id }}" data-confirm-text="{{ __('admin.delete') }}" data-title="{{ __('admin.delete') }}" data-message="{{ __('admin.confirm_message') }}"
                        class="w-full text-left px-4 py-3 text-sm text-red-700 hover:bg-red-50">{{ __('admin.delete') }}</button>
                <input type="hidden" name="_confirm_target" value="delete-cat-{{ $c->id }}" />
              </form>
            </div>
          </div>
        </div>

        <div class="mt-4 rounded-2xl bg-gray-50/70 border border-gray-100 p-3 flex items-center justify-between gap-2">
          <div class="flex items-center gap-2 min-w-0">
            <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-100 bg-white flex-shrink-0">
              <img src="{{ $icon }}"
                   class="w-full h-full object-cover js-fallback-img"
                   alt="icon"
                   data-fallback="{{ asset('assets/images/category-icon-placeholder.png') }}">
            </div>
            <div class="text-xs text-gray-600 truncate">{{ __('admin.sub') }}</div>
          </div>
          <div class="text-xs font-semibold text-gray-800">{{ $c->subcategories_count }}</div>
        </div>

        <div class="mt-auto pt-4 flex items-center justify-between">
          <form method="POST" action="{{ route('admin.categories.toggle', $c) }}" class="rtl-toggle">
            @csrf
            @method('PATCH')
            <button type="submit"
              class="w-10 h-6 rounded-full {{ $c->is_active ? 'bg-red-900' : 'bg-gray-200' }} relative transition">
              <span class="absolute top-0.5 {{ $c->is_active ? 'left-5' : 'left-0.5' }} w-5 h-5 rounded-full bg-white transition"></span>
            </button>
          </form>
          <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $c->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
            {{ $c->is_active ? __('admin.active') : __('admin.inactive') }}
          </span>
        </div>
      </div>
    @endforeach

  </div>

  <div>
    {{ $categories->links() }}
  </div>
</div>

<script>
  function toggleMenu(id){
    const el = document.getElementById(id);
    if(!el) return;
    el.classList.toggle('hidden');
  }

  document.addEventListener('click', function(e){
    document.querySelectorAll('[id^="catmenu-"]').forEach(m => {
      const btn = m.previousElementSibling;
      if(m.contains(e.target)) return;
      if(btn && btn.contains(e.target)) return;
      m.classList.add('hidden');
    });
  });

  document.querySelectorAll('.js-fallback-img').forEach(function(img) {
    img.addEventListener('error', function handleImgError() {
      const fallback = img.getAttribute('data-fallback');
      if (!fallback || img.src === fallback) return;
      img.src = fallback;
    }, { once: true });
  });
</script>
@endsection
