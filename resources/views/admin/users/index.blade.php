@extends('admin.layouts.app')

@section('title','Users')

@section('content')
<div class="space-y-6">

  {{-- Stats --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-[22px] bg-white border border-gray-100 shadow-soft p-5">
      <div class="text-sm text-gray-600">Total Users</div>
      <div class="mt-2 text-3xl font-semibold text-red-900">{{ method_exists($users, 'total') ? $users->total() : $users->count() }}</div>
    </div>
  </div>

  <div class="bg-white rounded-3xl shadow-soft p-6">

  {{-- Header: Title + Center Search + Right Icons --}}
  <div class="flex items-center justify-between gap-4 mb-6">
    <h2 class="text-xl font-semibold text-gray-900">Users</h2>

    {{-- Center Search like UI --}}
    <div class="flex-1 flex justify-center">
      <form method="GET" class="w-full max-w-xl">
        <div class="relative">
          {{-- Search icon --}}
          <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 rtl-search-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="11" cy="11" r="7"></circle>
              <path d="M21 21l-4.3-4.3"></path>
            </svg>
          </span>

          <input
            id="users_search"
            name="q"
            value="{{ request('q') }}"
            placeholder="Search"
            class="w-full h-12 rounded-full border border-gray-200 bg-gray-50/70 pl-12 pr-12 rtl-search-input
                   text-sm outline-none transition
                   focus:bg-white focus:border-red-300 focus:ring-4 focus:ring-red-100"
          >

          {{-- Clear button --}}
          @if(request('q'))
            <a href="{{ route('admin.users.index') }}"
               class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full
                      bg-white border border-gray-200 grid place-items-center text-gray-500
                      hover:text-gray-900 hover:bg-gray-50 transition"
               aria-label="Clear search">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18"></path>
                <path d="M6 6l12 12"></path>
              </svg>
            </a>
          @endif
        </div>
      </form>
    </div>

    {{-- Right icons like UI --}}
    <div class="flex items-center gap-3">
      <button type="button"
              class="w-11 h-11 rounded-full bg-white border border-gray-200 grid place-items-center text-red-900 shadow-sm hover:bg-gray-50 transition"
              aria-label="Settings">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M12 15.5A3.5 3.5 0 1 0 12 8.5a3.5 3.5 0 0 0 0 7z"/>
          <path d="M19.4 15a7.8 7.8 0 0 0 .1-2l2-1.2-2-3.4-2.3.7a7.6 7.6 0 0 0-1.7-1l-.3-2.4H9.8l-.3 2.4a7.6 7.6 0 0 0-1.7 1l-2.3-.7-2 3.4 2 1.2a7.8 7.8 0 0 0 .1 2l-2 1.2 2 3.4 2.3-.7c.5.4 1.1.7 1.7 1l.3 2.4h4.4l.3-2.4c.6-.3 1.2-.6 1.7-1l2.3.7 2-3.4-2-1.2z"/>
        </svg>
      </button>

      <button type="button"
              class="w-11 h-11 rounded-full bg-white border border-gray-200 grid place-items-center text-red-900 shadow-sm hover:bg-gray-50 transition"
              aria-label="Notifications">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M18 8a6 6 0 10-12 0c0 7-3 7-3 7h18s-3 0-3-7"/>
          <path d="M13.7 21a2 2 0 01-3.4 0"/>
        </svg>
      </button>
    </div>
  </div>

  {{-- Table --}}
  <div class="overflow-x-auto rounded-2xl border border-gray-100">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50/70">
        <tr class="text-left text-gray-500">
          <th class="py-4 px-5 font-medium">Name</th>
          <th class="py-4 px-5 font-medium">phone</th>
          <th class="py-4 px-5 font-medium">Gender</th>
          <th class="py-4 px-5 font-medium">Nationality</th>
          <th class="py-4 px-5 font-medium">City</th>
          <th class="py-4 px-5 font-medium">Reviews</th>
          <th class="py-4 px-5 font-medium text-right">Actions</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100 bg-white">
        @forelse($users as $u)
          <tr class="hover:bg-gray-50/60 transition">
            {{-- Name cell with avatar --}}
            <td class="py-5 px-5">
              <div class="flex items-center gap-3">
                @php
                  $img = $u->avatar ?? ($u->avatar_path ?? ($u->photo_path ?? null));
                  $initial = strtoupper(mb_substr($u->full_name ?? $u->name ?? 'U', 0, 1));
                @endphp

                @if($img)
                  <div class="w-11 h-11 rounded-full overflow-hidden bg-gray-100">
                    <img
                      src="{{ (file_exists(public_path($img)) ? asset($img) : (\Illuminate\Support\Facades\Storage::disk('public')->exists($img) ? route('storage.proxy',['path'=>$img]) : asset('assets/images/userdefultphoto.png'))) }}"
                      class="w-full h-full object-cover"
                      alt="avatar"
                    >
                  </div>
                @else
                  <div class="w-11 h-11 rounded-full bg-gray-200 grid place-items-center text-gray-600 font-semibold">
                    {{ $initial }}
                  </div>
                @endif

                <div class="leading-tight">
                  <div class="font-semibold text-gray-900">{{ $u->full_name ?? $u->name }}</div>
                  <div class="text-xs text-gray-500">{{ $u->email }}</div>
                </div>
              </div>
            </td>

            <td class="py-5 px-5 text-gray-700">{{ $u->phone ?? '-' }}</td>

            <td class="py-5 px-5 text-gray-700">
              {{ optional($u->gender)->name_en ?? optional($u->gender)->name ?? optional($u->gender)->code ?? ($u->gender ?? '-') }}
            </td>

            <td class="py-5 px-5 text-gray-700">
              {{ optional($u->nationality)->name_en ?? optional($u->nationality)->country_name ?? optional($u->nationality)->name ?? optional($u->nationality)->iso_code ?? ($u->nationality ?? '-') }}
            </td>

            <td class="py-5 px-5 text-gray-700">
              {{ $u->city ?? '-' }}
            </td>

            <td class="py-5 px-5">
              <a href="{{ route('admin.users.show', $u) }}"
                 class="inline-flex items-center justify-center min-w-[110px] px-4 py-2 rounded-full
                        bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100 transition">
                <span class="mr-2">{{ $u->reviews_count ?? 0 }}</span>
                Reviews
              </a>
            </td>

            <td class="py-5 px-5 text-right">
              <a href="{{ route('admin.users.show', $u) }}"
                 class="inline-flex items-center px-4 py-2 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold
                        hover:bg-gray-200 transition">
                View
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="py-14 text-center text-gray-500">No users found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-5">
    {{ $users->withQueryString()->links() }}
  </div>
</div>

{{-- Auto-submit on typing --}}
<script>
  (function () {
    const input = document.getElementById('users_search');
    if (!input) return;

    let t = null;
    input.addEventListener('input', function () {
      clearTimeout(t);
      t = setTimeout(() => {
        input.closest('form').submit();
      }, 400);
    });
  })();
</script>
@endsection
