@extends('admin.layouts.app')

@section('title','Banners & Onboarding')

@section('content')
<div class="space-y-8">

  {{-- Header row --}}
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <h2 class="text-2xl font-semibold text-gray-900">Banners & Onboarding</h2>

    <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
      <div class="w-full max-w-md">
        <form method="get" class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="11" cy="11" r="7"></circle>
              <path d="M21 21l-4.3-4.3"></path>
            </svg>
          </span>
          <input
            name="q"
            value="{{ request('q') }}"
            placeholder="Search"
            class="w-full rounded-2xl border border-gray-200 bg-white/80 pl-11 pr-4 py-3 text-sm outline-none
                   focus:border-red-300 focus:ring-4 focus:ring-red-100 transition"
          >
        </form>
      </div>
    </div>
  </div>

  {{-- Banners --}}
  <div class="space-y-4">
    <div class="text-sm font-semibold text-gray-900">Banners</div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <a href="{{ route('admin.banners.create') }}"
         class="group rounded-[26px] border-2 border-dashed border-gray-200 bg-white/60 hover:bg-white
                hover:border-gray-300 transition shadow-soft p-6 flex flex-col items-center justify-center min-h-[210px]">
        <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 grid place-items-center text-gray-700 group-hover:scale-105 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"/>
          </svg>
        </div>
        <div class="mt-3 text-sm font-semibold text-gray-900">Add Banner</div>
      </a>

      @foreach($banners as $b)
        @php
          $img = $b->image ? asset($b->image) : asset('assets/images/category-placeholder.png');
        @endphp
        <div class="rounded-[26px] bg-white border border-gray-100 shadow-soft overflow-hidden">
          <div class="relative h-32 bg-gray-100">
            <img src="{{ $img }}" alt="{{ $b->offer_name }}" class="w-full h-full object-cover">

            <form method="POST" action="{{ route('admin.banners.toggle', $b) }}" class="absolute top-3 left-3">
              @csrf
              @method('PATCH')
              <button type="submit"
                class="w-10 h-6 rounded-full {{ $b->is_active ? 'bg-red-900' : 'bg-gray-200' }} relative transition">
                <span class="absolute top-0.5 {{ $b->is_active ? 'left-5' : 'left-0.5' }} w-5 h-5 rounded-full bg-white transition"></span>
              </button>
            </form>

            <div class="absolute top-3 right-3">
              <button type="button" class="w-9 h-9 rounded-full bg-white/90 border border-gray-100 grid place-items-center text-gray-700 hover:bg-white"
                      onclick="toggleMenu('bannermenu-{{ $b->id }}')">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/>
                </svg>
              </button>

              <div id="bannermenu-{{ $b->id }}" class="hidden absolute right-0 mt-2 w-44 rounded-2xl bg-white border border-gray-100 shadow-lg overflow-hidden">
                <a href="{{ route('admin.banners.show', $b) }}" class="block px-4 py-3 text-sm hover:bg-gray-50">Show</a>
                <a href="{{ route('admin.banners.edit', $b) }}" class="block px-4 py-3 text-sm hover:bg-gray-50">Edit</a>
                <form method="POST" action="{{ route('admin.banners.destroy', $b) }}">
                  @csrf
                  @method('DELETE')
                  <button type="button" data-confirm="delete-banner-{{ $b->id }}" data-confirm-text="Delete" data-title="Delete banner?" data-message="Are you sure you want to delete '{{ $b->offer_name }}'?"
                          class="w-full text-left px-4 py-3 text-sm text-red-700 hover:bg-red-50">Delete</button>
                  <input type="hidden" name="_confirm_target" value="delete-banner-{{ $b->id }}" />
                </form>
              </div>
            </div>
          </div>

          <div class="p-4">
            <div class="text-sm font-semibold text-gray-900 truncate">{{ $b->offer_name }}</div>
            <div class="text-[11px] text-gray-500 mt-1 truncate">{{ $b->brand?->name_en ?: 'Vendor' }}</div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Onboarding --}}
  <div class="space-y-4">
    <div class="text-sm font-semibold text-gray-900">Onboarding</div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <a href="{{ route('admin.onboardings.create') }}"
         class="group rounded-[26px] border-2 border-dashed border-gray-200 bg-white/60 hover:bg-white
                hover:border-gray-300 transition shadow-soft p-6 flex flex-col items-center justify-center min-h-[210px]">
        <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 grid place-items-center text-gray-700 group-hover:scale-105 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"/>
          </svg>
        </div>
        <div class="mt-3 text-sm font-semibold text-gray-900">Add Onboarding</div>
      </a>

      @foreach($onboardings as $o)
        @php
          $img = $o->image ? asset($o->image) : asset('assets/images/category-placeholder.png');
        @endphp
        <div class="rounded-[26px] bg-white border border-gray-100 shadow-soft overflow-hidden">
          <div class="relative h-32 bg-gray-100">
            <img src="{{ $img }}" alt="{{ $o->title }}" class="w-full h-full object-cover">

            <form method="POST" action="{{ route('admin.onboardings.toggle', $o) }}" class="absolute top-3 left-3">
              @csrf
              @method('PATCH')
              <button type="submit"
                class="w-10 h-6 rounded-full {{ $o->is_active ? 'bg-red-900' : 'bg-gray-200' }} relative transition">
                <span class="absolute top-0.5 {{ $o->is_active ? 'left-5' : 'left-0.5' }} w-5 h-5 rounded-full bg-white transition"></span>
              </button>
            </form>

            <div class="absolute top-3 right-3">
              <button type="button" class="w-9 h-9 rounded-full bg-white/90 border border-gray-100 grid place-items-center text-gray-700 hover:bg-white"
                      onclick="toggleMenu('onboardmenu-{{ $o->id }}')">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/>
                </svg>
              </button>

              <div id="onboardmenu-{{ $o->id }}" class="hidden absolute right-0 mt-2 w-44 rounded-2xl bg-white border border-gray-100 shadow-lg overflow-hidden">
                <a href="{{ route('admin.onboardings.show', $o) }}" class="block px-4 py-3 text-sm hover:bg-gray-50">Show</a>
                <a href="{{ route('admin.onboardings.edit', $o) }}" class="block px-4 py-3 text-sm hover:bg-gray-50">Edit</a>
                <form method="POST" action="{{ route('admin.onboardings.destroy', $o) }}">
                  @csrf
                  @method('DELETE')
                  <button type="button" data-confirm="delete-onboarding-{{ $o->id }}" data-confirm-text="Delete" data-title="Delete onboarding?" data-message="Are you sure you want to delete '{{ $o->title }}'?"
                          class="w-full text-left px-4 py-3 text-sm text-red-700 hover:bg-red-50">Delete</button>
                  <input type="hidden" name="_confirm_target" value="delete-onboarding-{{ $o->id }}" />
                </form>
              </div>
            </div>
          </div>

          <div class="p-4">
            <div class="text-sm font-semibold text-gray-900 truncate">{{ $o->title }}</div>
            <div class="text-[11px] text-gray-500 mt-1 truncate">{{ $o->subtitle ?: '-' }}</div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<script>
  function toggleMenu(id){
    const el = document.getElementById(id);
    if(!el) return;
    el.classList.toggle('hidden');
  }
  document.addEventListener('click', function(e){
    document.querySelectorAll('[id^="bannermenu-"], [id^="onboardmenu-"]').forEach(m => {
      const btn = m.previousElementSibling;
      if(m.contains(e.target)) return;
      if(btn && btn.contains(e.target)) return;
      m.classList.add('hidden');
    });
  });
</script>
@endsection
