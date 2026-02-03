@extends('admin.layouts.app')

@section('title','Branches')

@section('content')
<div class="space-y-6">

  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <h2 class="text-2xl font-semibold text-gray-900">Branches</h2>

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
            placeholder="Search"
            class="w-full rounded-2xl border border-gray-200 bg-white/80 pl-11 pr-4 py-3 text-sm outline-none rtl-search-input
                   focus:border-red-300 focus:ring-4 focus:ring-red-100 transition"
          >
        </form>
      </div>


    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-[22px] bg-red-900 text-white p-5 shadow-soft">
      <div class="text-sm opacity-90">Total Branches</div>
      <div class="mt-2 text-3xl font-semibold">{{ $totalBranches }}</div>
    </div>
    <div class="rounded-[22px] bg-white p-5 border border-gray-100 shadow-soft">
      <div class="text-sm text-gray-600">Active Branches</div>
      <div class="mt-2 text-3xl font-semibold text-red-900">{{ $activeBranches }}</div>
    </div>
    <div class="rounded-[22px] bg-white p-5 border border-gray-100 shadow-soft">
      <div class="text-sm text-gray-600">Inactive Branches</div>
      <div class="mt-2 text-3xl font-semibold text-red-900">{{ $inactiveBranches }}</div>
    </div>
  </div>

  <div class="flex items-center gap-3">
    <a href="{{ route('admin.branches.index') }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold {{ request('status') ? 'bg-white text-gray-600 border border-gray-100' : 'bg-red-900 text-white' }}">
      All Branches
    </a>
    <a href="{{ route('admin.branches.index', ['status' => 'active']) }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold {{ request('status') === 'active' ? 'bg-red-900 text-white' : 'bg-white text-gray-600 border border-gray-100' }}">
      Active
    </a>
    <a href="{{ route('admin.branches.index', ['status' => 'inactive']) }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold {{ request('status') === 'inactive' ? 'bg-red-900 text-white' : 'bg-white text-gray-600 border border-gray-100' }}">
      Inactive
    </a>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

    <a href="{{ route('admin.branches.create') }}"
       class="group rounded-[26px] border-2 border-dashed border-gray-200 bg-white/60 hover:bg-white
              hover:border-gray-300 transition shadow-soft p-6 flex flex-col items-center justify-center min-h-[220px]">
      <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 grid place-items-center text-gray-700 group-hover:scale-105 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 5v14M5 12h14"/>
        </svg>
      </div>
      <div class="mt-3 text-sm font-semibold text-gray-900">Add Branch</div>
      <div class="text-xs text-gray-500 mt-1">Create a new branch</div>
    </a>

    @foreach($branches as $br)
      @php
        $placeName = $br->place?->display_name ?: 'Place';
        $brandName = $br->place?->brand?->name_en ?: '-';
      @endphp

      <div class="rounded-[26px] bg-white border border-gray-100 shadow-soft overflow-hidden">
        <div class="relative h-28 bg-gradient-to-br from-red-50 to-white">
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-14 h-14 rounded-2xl bg-white border border-gray-100 grid place-items-center text-red-900 font-semibold shadow-sm">
              {{ strtoupper(mb_substr($placeName, 0, 1)) }}
            </div>
          </div>

          <form method="POST" action="{{ route('admin.branches.toggle', $br) }}" class="absolute top-3 left-3 rtl-toggle">
            @csrf
            @method('PATCH')
            <button type="submit"
              class="w-10 h-6 rounded-full {{ $br->is_active ? 'bg-red-900' : 'bg-gray-200' }} relative transition">
              <span class="absolute top-0.5 {{ $br->is_active ? 'left-5' : 'left-0.5' }} w-5 h-5 rounded-full bg-white transition"></span>
            </button>
          </form>

          <div class="absolute top-3 right-3 rtl-dots">
            <button type="button" class="w-9 h-9 rounded-full bg-white/90 border border-gray-100 grid place-items-center text-gray-700 hover:bg-white"
                    onclick="toggleMenu('branchmenu-{{ $br->id }}')">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <circle cx="12" cy="5" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="12" cy="19" r="1.6"/>
              </svg>
            </button>

            <div id="branchmenu-{{ $br->id }}" class="hidden absolute right-0 mt-2 w-44 rounded-2xl bg-white border border-gray-100 shadow-lg overflow-hidden rtl-menu-right">
              <a href="{{ route('admin.branches.show', $br) }}" class="block px-4 py-3 text-sm hover:bg-gray-50">Show</a>
              <a href="{{ route('admin.branches.edit', $br) }}" class="block px-4 py-3 text-sm hover:bg-gray-50">Edit</a>
              <form method="POST" action="{{ route('admin.branches.destroy', $br) }}">
                @csrf
                @method('DELETE')
                <button type="button" data-confirm="delete-branch-{{ $br->id }}" data-confirm-text="Delete" data-title="Delete branch?" data-message="Are you sure you want to delete this branch?"
                        class="w-full text-left px-4 py-3 text-sm text-red-700 hover:bg-red-50">Delete</button>
                <input type="hidden" name="_confirm_target" value="delete-branch-{{ $br->id }}" />
              </form>
            </div>
          </div>
        </div>

        <div class="p-4">
          <div class="flex items-center justify-between gap-2">
            <div class="min-w-0">
              <div class="text-sm font-semibold text-gray-900 truncate">{{ $placeName }}</div>
              <div class="text-[11px] text-gray-500 truncate">{{ $brandName }}</div>
              <div class="text-[11px] text-gray-500 truncate mt-1">{{ $br->address }}</div>
            </div>
            <div class="text-[11px] text-gray-500 whitespace-nowrap">
              {{ $br->review_cooldown_days ?? 0 }} days
            </div>
          </div>
        </div>
      </div>
    @endforeach

  </div>

  <div>
    {{ $branches->links() }}
  </div>
</div>

<script>
  function toggleMenu(id){
    const el = document.getElementById(id);
    if(!el) return;
    el.classList.toggle('hidden');
  }
  document.addEventListener('click', function(e){
    document.querySelectorAll('[id^="branchmenu-"]').forEach(m => {
      const btn = m.previousElementSibling;
      if(m.contains(e.target)) return;
      if(btn && btn.contains(e.target)) return;
      m.classList.add('hidden');
    });
  });
</script>
@endsection
