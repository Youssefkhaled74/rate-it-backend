@extends('admin.layouts.app')

@section('title','Users')

@section('content')
<div class="space-y-6">

  {{-- Statistics Overview --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-[22px] bg-red-900 text-white p-5 shadow-soft">
      <div class="text-sm opacity-90">{{ __('admin.total_users') }}</div>
      <div class="mt-2 text-3xl font-semibold">{{ $stats['total'] ?? 0 }}</div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-sm text-gray-600">{{ __('admin.new_users_7') }}</div>
      <div class="mt-2 text-3xl font-semibold text-red-900">{{ $stats['new_7'] ?? 0 }}</div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-sm text-gray-600">{{ __('admin.active_users') }}</div>
      <div class="mt-2 text-3xl font-semibold text-red-900">{{ $stats['active'] ?? 0 }}</div>
    </div>
    <div class="rounded-[22px] bg-white border border-gray-100 p-5 shadow-soft">
      <div class="text-sm text-gray-600">{{ __('admin.inactive_users') }}</div>
      <div class="mt-2 text-3xl font-semibold text-red-900">{{ $stats['inactive'] ?? 0 }}</div>
    </div>
  </div>

  {{-- Breakdown Cards --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-3xl shadow-soft p-6 border border-gray-100">
      <div class="text-sm font-semibold text-gray-900">{{ __('admin.users_by_gender') }}</div>
      <div class="mt-4 space-y-3">
        @forelse($genderStats as $row)
          <div>
            <div class="flex items-center justify-between text-xs text-gray-600">
              <span class="truncate">{{ $row['name'] }}</span>
              <span class="font-semibold text-gray-800">{{ $row['total'] }}</span>
            </div>
            <div class="mt-2 h-2 rounded-full bg-gray-100">
              <div class="h-2 rounded-full bg-red-900"
                   style="width: {{ $stats['total'] > 0 ? round(($row['total'] / $stats['total']) * 100) : 0 }}%"></div>
            </div>
          </div>
        @empty
          <div class="text-sm text-gray-500">{{ __('admin.no_data') }}</div>
        @endforelse
      </div>
    </div>

    <div class="bg-white rounded-3xl shadow-soft p-6 border border-gray-100">
      <div class="text-sm font-semibold text-gray-900">{{ __('admin.users_by_nationality') }}</div>
      <div class="mt-4 space-y-3">
        @forelse($nationalityStats as $row)
          <div>
            <div class="flex items-center justify-between text-xs text-gray-600">
              <span class="truncate">{{ $row['name'] }}</span>
              <span class="font-semibold text-gray-800">{{ $row['total'] }}</span>
            </div>
            <div class="mt-2 h-2 rounded-full bg-gray-100">
              <div class="h-2 rounded-full bg-red-900"
                   style="width: {{ $stats['total'] > 0 ? round(($row['total'] / $stats['total']) * 100) : 0 }}%"></div>
            </div>
          </div>
        @empty
          <div class="text-sm text-gray-500">{{ __('admin.no_data') }}</div>
        @endforelse
      </div>
    </div>

    <div class="bg-white rounded-3xl shadow-soft p-6 border border-gray-100">
      <div class="text-sm font-semibold text-gray-900">{{ __('admin.reviews_engagement') }}</div>
      <div class="mt-4 grid grid-cols-2 gap-4">
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4 text-center">
          <div class="text-xs text-gray-500">{{ __('admin.with_reviews') }}</div>
          <div class="text-2xl font-semibold text-red-900 mt-1">{{ $stats['with_reviews'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4 text-center">
          <div class="text-xs text-gray-500">{{ __('admin.no_reviews') }}</div>
          <div class="text-2xl font-semibold text-red-900 mt-1">{{ $stats['without_reviews'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-3xl shadow-soft p-6">

  {{-- Header: Title + Center Search + Right Icons --}}
  <div class="flex items-center justify-between gap-4 mb-6">
    <h2 class="text-xl font-semibold text-gray-900">{{ __('admin.users') }}</h2>

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
            placeholder="{{ __('admin.search') }}"
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

    {{-- Right actions like UI --}}
    <div class="flex items-center gap-3">
      <a href="{{ route('admin.users.export', request()->query()) }}"
         class="h-11 inline-flex items-center gap-2 rounded-full bg-white border border-gray-200 px-4 text-sm font-semibold text-red-900 shadow-sm hover:bg-gray-50 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M12 3v12"/>
          <path d="M8 11l4 4 4-4"/>
          <path d="M20 21H4"/>
        </svg>
        Export
      </a>
    </div>
  </div>

  {{-- Table --}}
  <div class="overflow-x-auto rounded-2xl border border-gray-100">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50/70">
        <tr class="text-left text-gray-500">
          <th class="py-4 px-5 font-medium">{{ __('admin.name') }}</th>
          <th class="py-4 px-5 font-medium">{{ __('admin.phone') }}</th>
          <th class="py-4 px-5 font-medium">{{ __('admin.gender') }}</th>
          <th class="py-4 px-5 font-medium">{{ __('admin.nationality') }}</th>
          <th class="py-4 px-5 font-medium">{{ __('admin.city') }}</th>
          <th class="py-4 px-5 font-medium">{{ __('admin.reviews') }}</th>
          <th class="py-4 px-5 font-medium text-right">{{ __('admin.actions') }}</th>
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
                  $initial = strtoupper(mb_substr($u->name ?? 'U', 0, 1));
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
                  <div class="w-11 h-11 rounded-full overflow-hidden bg-gray-100">
                    <img
                      src="{{ asset('assets/images/userdefultphoto.png') }}"
                      class="w-full h-full object-cover"
                      alt="avatar"
                    >
                  </div>
                @endif

                <div class="leading-tight">
                  <div class="font-semibold text-gray-900">{{ $u->name }}</div>
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
              {{ $u->city?->name_en ?? $u->city?->name_ar ?? '-' }}
            </td>

            <td class="py-5 px-5">
              <a href="{{ route('admin.users.show', $u) }}"
                 class="inline-flex items-center justify-center min-w-[110px] px-4 py-2 rounded-full
                        bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100 transition">
                <span class="mr-2">{{ $u->reviews_count ?? 0 }}</span>
                {{ __('admin.reviews') }}
              </a>
            </td>

            <td class="py-5 px-5 text-right">
              <a href="{{ route('admin.users.show', $u) }}"
                 class="inline-flex items-center px-4 py-2 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold
                        hover:bg-gray-200 transition">
                {{ __('admin.view') }}
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="py-14 text-center text-gray-500">{{ __('admin.no_users_found') }}</td>
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
