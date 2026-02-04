@extends('admin.layouts.app')

@section('title','Admins')

@section('content')
<div class="bg-white rounded-3xl shadow-soft p-6">

  {{-- Header --}}
  <div class="flex items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-semibold">Admins</h2>
      <p class="text-sm text-gray-500 mt-1">Manage admin accounts, roles, and status.</p>
    </div>

    <a href="{{ route('admin.admins.create') }}"
       class="rounded-2xl bg-red-800 text-white px-4 py-2.5 text-sm font-semibold
              hover:bg-red-900 transition shadow-lg shadow-red-800/20">
      Add Admin
    </a>
  </div>

  {{-- Filters --}}
  <div class="mt-5">
    <form method="get" class="flex flex-col md:flex-row gap-3">
      <div class="flex-1 relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
          {{-- search icon --}}
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="M21 21l-4.3-4.3"></path>
          </svg>
        </span>
        <input name="q" value="{{ request('q') }}"
               placeholder="Search name, email or phone"
               class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 pl-11 pr-4 py-3 text-sm outline-none
                      focus:border-red-300 focus:ring-4 focus:ring-red-100 transition">
      </div>

            <x-admin.select
        name="status"
        placeholder="All"
        selectClass="bg-gray-50/50 border-gray-200"
      >
        <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
      </x-admin.select>

      <button class="rounded-2xl bg-gray-900 text-white px-5 py-3 text-sm font-semibold hover:opacity-90 transition">
        Filter
      </button>

      @if(request()->hasAny(['q','status']))
        <a href="{{ route('admin.admins.index') }}"
           class="rounded-2xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
          Reset
        </a>
      @endif
    </form>
  </div>

  {{-- Table --}}
  <div class="mt-6 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-gray-500">
          <th class="py-2 font-medium">Name</th>
          <th class="py-2 font-medium">phone</th>
          <th class="py-2 font-medium">Role</th>
          <th class="py-2 font-medium">Status</th>
          <th class="py-2 font-medium">Created</th>
          <th class="py-2 font-medium text-right">Actions</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100">
        @forelse($admins as $a)
          <tr class="hover:bg-gray-50/60 transition">
            {{-- Name (compact) --}}
            <td class="py-2">
              <div class="flex items-center gap-3">
                {{-- Avatar --}}
                @if(!empty($a->photo_url))
                  <div class="w-9 h-9 rounded-full overflow-hidden">
                    <img src="{{ $a->photo_url }}" alt="{{ $a->name }}" class="w-9 h-9 object-cover">
                  </div>
                @else
                  <div class="w-9 h-9 rounded-full bg-gray-200 overflow-hidden grid place-items-center text-gray-600 font-semibold text-sm">
                    {{ strtoupper(mb_substr($a->name ?? 'A', 0, 1)) }}
                  </div>
                @endif

                <div class="leading-tight">
                  <div class="font-medium text-gray-900 text-sm">{{ $a->name }}</div>
                </div>
              </div>
            </td>

            <td class="py-2 text-gray-700 text-sm">
              {{ $a->phone ?? '-' }}
            </td>

            <td class="py-2 text-gray-700 text-sm">
              <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-xs">
                {{ $a->role ?? 'admin' }}
              </span>
            </td>

            {{-- Status pill --}}
            <td class="py-2">
              @if($a->is_active)
                <span class="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-green-50 text-green-700 text-xs">
                  <span class="w-2 h-2 rounded-full bg-green-600"></span>
                  Active
                </span>
              @else
                <span class="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-gray-100 text-gray-600 text-xs">
                  <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                  Inactive
                </span>
              @endif
            </td>

            <td class="py-2 text-gray-500 text-sm">
              {{ $a->created_at?->diffForHumans() ?? '-' }}
            </td>

            {{-- Actions (pills like UI) --}}
            <td class="py-2">
              <div class="flex items-center justify-end gap-2">

                @can('update', $a)
                  <a href="{{ route('admin.admins.edit', $a) }}"
                    class="px-3 py-1.5 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold hover:bg-gray-200 transition">
                    Edit
                  </a>
                @endcan

                @can('toggle', $a)
                  <form method="POST" action="{{ route('admin.admins.toggle', $a) }}">
                    @csrf
                    @method('PATCH')
                    <button
                      class="px-3 py-1.5 rounded-full text-xs font-semibold transition
                             {{ $a->is_active ? 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                      {{ $a->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                  </form>
                @endcan

                @can('delete', $a)
                  <form method="POST" action="{{ route('admin.admins.destroy', $a) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            data-confirm="delete-admin"
                            data-title="Delete admin?"
                            data-message="This action cannot be undone."
                            data-confirm-text="Delete"
                            class="px-3 py-1.5 rounded-full bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100 transition">
                      Delete
                    </button>
                  </form>
                @endcan

              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-10 text-center text-gray-500">
              No admins found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-6">
    {{ $admins->withQueryString()->links() }}
  </div>
</div>
@endsection

