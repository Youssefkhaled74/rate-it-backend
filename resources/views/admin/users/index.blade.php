@extends('admin.layouts.app')

@section('title','Users')

@section('content')
<div class="bg-white rounded-3xl shadow-soft p-6">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-semibold">Users</h2>
    <div class="w-1/2">
      <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="M21 21l-4.3-4.3"></path>
          </svg>
        </span>
        <form method="get">
          <input name="q" value="{{ request('q') }}" placeholder="Search name, email or phone"
            class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 pl-11 pr-4 py-3 text-sm outline-none focus:border-red-300 focus:ring-4 focus:ring-red-100 transition">
        </form>
      </div>
    </div>
  </div>

  <div class="mt-4 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-gray-500">
          <th class="py-3 font-medium">Name</th>
          <th class="py-3 font-medium">phone</th>
          <th class="py-3 font-medium">gender</th>
          <th class="py-3 font-medium">nationality</th>
          <th class="py-3 font-medium">city</th>
          <th class="py-3 font-medium">reviews</th>
          <th class="py-3 font-medium text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($users as $u)
          <tr class="hover:bg-gray-50/60 transition">
            <td class="py-3">
              <div class="flex items-center gap-3">
                @if(!empty($u->avatar) || !empty($u->avatar_path) || !empty($u->photo_path))
                  @php
                    $img = $u->avatar ?? ($u->avatar_path ?? ($u->photo_path ?? null));
                  @endphp
                  @if($img)
                    <div class="w-11 h-11 rounded-full overflow-hidden">
                      <img src="{{ (file_exists(public_path($img)) ? asset($img) : (\Illuminate\Support\Facades\Storage::disk('public')->exists($img) ? route('storage.proxy',['path'=>$img]) : '') ) }}" class="w-11 h-11 object-cover">
                    </div>
                  @else
                    <div class="w-11 h-11 rounded-full bg-gray-200 grid place-items-center text-gray-600 font-semibold">{{ strtoupper(mb_substr($u->name ?? $u->full_name ?? 'U',0,1)) }}</div>
                  @endif
                @else
                  <div class="w-11 h-11 rounded-full bg-gray-200 grid place-items-center text-gray-600 font-semibold">{{ strtoupper(mb_substr($u->name ?? $u->full_name ?? 'U',0,1)) }}</div>
                @endif

                <div class="leading-tight">
                  <div class="font-semibold text-gray-900">{{ $u->full_name ?? $u->name }}</div>
                  <div class="text-xs text-gray-500">{{ $u->email }}</div>
                </div>
              </div>
            </td>
            <td class="py-3 text-gray-700">{{ $u->phone ?? '-' }}</td>
            <td class="py-3 text-gray-700">{{ optional($u->gender)->name ?? '-' }}</td>
            <td class="py-3 text-gray-700">{{ optional($u->nationality)->name ?? '-' }}</td>
            <td class="py-3 text-gray-700">{{ $u->city ?? '-' }}</td>
            <td class="py-3">
              <a href="{{ route('admin.users.show', $u) }}" class="inline-flex items-center px-3 py-1 rounded-full bg-red-50 text-red-700 text-xs font-semibold">
                {{ $u->reviews_count ?? 0 }} Reviews
              </a>
            </td>
            <td class="py-3 text-right">
              <a href="{{ route('admin.users.show', $u) }}" class="px-3 py-1.5 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold">View</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="py-10 text-center text-gray-500">No users found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $users->withQueryString()->links() }}</div>
</div>
@endsection
