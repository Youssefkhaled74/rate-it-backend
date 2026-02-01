@extends('admin.layouts.app')

@section('title','Categories')

@section('content')
<div class="space-y-6">

  {{-- Header row: title + search (like UI) --}}
  <div class="flex items-center justify-between gap-4">
    <h2 class="text-xl font-semibold text-gray-900">Categories</h2>

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
          class="w-full rounded-2xl border border-gray-200 bg-white/70 pl-11 pr-4 py-3 text-sm outline-none
                 focus:border-red-300 focus:ring-4 focus:ring-red-100 transition"
        >
      </form>
    </div>
  </div>

  {{-- Grid --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

    {{-- Add Card --}}
    <a href="{{ route('admin.categories.create') }}"
       class="group rounded-[26px] border-2 border-dashed border-gray-200 bg-white/60 hover:bg-white
              hover:border-gray-300 transition shadow-soft p-6 flex flex-col items-center justify-center min-h-[210px]">
      <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 grid place-items-center text-gray-700 group-hover:scale-105 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 5v14M5 12h14"/>
        </svg>
      </div>
      <div class="mt-3 text-sm font-semibold text-gray-900">Add Category</div>
      <div class="text-xs text-gray-500 mt-1">Create a new category</div>
    </a>

    {{-- Category Cards --}}
    @foreach($categories as $c)
      @php
        $img = $c->logo ? asset($c->logo) : asset('assets/images/category-placeholder.png');
      @endphp

      <div class="rounded-[26px] bg-white border border-gray-100 shadow-soft overflow-hidden">
        {{-- image --}}
        <div class="relative h-28 bg-gray-100">
          <img src="{{ $img }}" alt="{{ $c->name_en }}" class="w-full h-full object-cover">

          {{-- toggle (top-left small) --}}
          <form method="POST" action="{{ route('admin.categories.toggle', $c) }}" class="absolute top-3 left-3">
            @csrf
            @method('PATCH')
            <button type="submit"
              class="w-10 h-6 rounded-full {{ $c->is_active ? 'bg-red-900' : 'bg-gray-200' }} relative transition">
              <span class="absolute top-0.5 {{ $c->is_active ? 'left-5' : 'left-0.5' }} w-5 h-5 rounded-full bg-white transition"></span>
            </button>
          </form>

          {{-- menu (top-right) --}}
          <div class="absolute top-3 right-3">
            <button type="button" class="w-9 h-9 rounded-full bg-white/90 border border-gray-100 grid place-items-center text-gray-700 hover:bg-white"
                    onclick="toggleMenu('catmenu-{{ $c->id }}')">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/>
              </svg>
            </button>

              <div id="catmenu-{{ $c->id }}" class="hidden absolute right-0 mt-2 w-44 rounded-2xl bg-white border border-gray-100 shadow-lg overflow-hidden">
              <a href="{{ route('admin.categories.edit', $c) }}" class="block px-4 py-3 text-sm hover:bg-gray-50">Edit</a>
              <form method="POST" action="{{ route('admin.categories.destroy', $c) }}">
                @csrf
                @method('DELETE')
                <button type="button" data-confirm="delete-cat-{{ $c->id }}" data-confirm-text="Delete" data-title="Delete category?" data-message="Are you sure you want to delete the category '{{ $c->name_en }}'?"
                        class="w-full text-left px-4 py-3 text-sm text-red-700 hover:bg-red-50">Delete</button>
                <input type="hidden" name="_confirm_target" value="delete-cat-{{ $c->id }}" />
              </form>
            </div>
          </div>
        </div>

        {{-- body --}}
        <div class="p-4">
          <div class="flex items-center justify-between gap-2">
            <div class="min-w-0">
              <div class="text-sm font-semibold text-gray-900 truncate">
                {{ $c->name_en }}
              </div>
              <div class="text-[11px] text-gray-500 truncate">
                {{ $c->name_ar ?: '—' }}
              </div>
            </div>

            <div class="text-[11px] text-gray-500 whitespace-nowrap">
              {{ $c->subcategories_count }} Sub
            </div>
          </div>
        </div>
      </div>
    @endforeach

  </div>

  {{-- Pagination --}}
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
  // close menus on outside click
  document.addEventListener('click', function(e){
    document.querySelectorAll('[id^="catmenu-"]').forEach(m => {
      const btn = m.previousElementSibling;
      if(m.contains(e.target)) return;
      if(btn && btn.contains(e.target)) return;
      m.classList.add('hidden');
    });
  });
</script>
@endsection
