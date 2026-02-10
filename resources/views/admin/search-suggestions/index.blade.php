@extends('admin.layouts.app')

@section('page_title', __('admin.search_suggestions'))
@section('title', __('admin.search_suggestions'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.search_suggestions') }}</h2>
        <div class="text-sm text-gray-500 mt-1">{{ __('admin.search_suggestions_hint') }}</div>
      </div>
      <a href="{{ route('admin.search-suggestions.create') }}"
         class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">
        {{ __('admin.add_suggestion') }}
      </a>
    </div>

    @if(session('success'))
      <div class="mt-4 rounded-2xl bg-green-50 border border-green-100 text-green-700 text-sm px-4 py-3">
        {{ session('success') }}
      </div>
    @endif

    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-3">
      <input name="q" value="{{ $q }}" placeholder="{{ __('admin.search') }}"
             class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      <select name="active" class="rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        <option value="">{{ __('admin.all') }}</option>
        <option value="1" {{ (string)$active === '1' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
        <option value="0" {{ (string)$active === '0' ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
      </select>
      <div class="flex items-center gap-2">
        <button type="submit" class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">
          {{ __('admin.filter') }}
        </button>
        <a href="{{ route('admin.search-suggestions.index') }}" class="text-sm text-gray-500">{{ __('admin.reset') }}</a>
      </div>
    </form>
  </div>

  <div class="mt-5 bg-white border border-gray-100 rounded-[24px] p-4 shadow-soft">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-xs uppercase text-gray-400 border-b">
          <tr>
            <th class="text-left py-3 px-2">{{ __('admin.term_en') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.term_ar') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.sort_order') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.status') }}</th>
            <th class="text-left py-3 px-2">{{ __('admin.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($suggestions as $s)
            <tr>
              <td class="py-3 px-2 text-gray-900 font-semibold">{{ $s->term_en ?? '-' }}</td>
              <td class="py-3 px-2 text-gray-700">{{ $s->term_ar ?? '-' }}</td>
              <td class="py-3 px-2 text-gray-700">{{ $s->sort_order ?? 0 }}</td>
              <td class="py-3 px-2">
                <form method="POST" action="{{ route('admin.search-suggestions.toggle', $s) }}">
                  @csrf
                  @method('PATCH')
                  <button type="submit"
                    class="px-3 py-1 rounded-full text-xs font-semibold border {{ $s->is_active ? 'border-emerald-200 text-emerald-700' : 'border-gray-200 text-gray-600' }}">
                    {{ $s->is_active ? __('admin.active') : __('admin.inactive') }}
                  </button>
                </form>
              </td>
              <td class="py-3 px-2">
                <div class="flex items-center gap-2">
                  <a href="{{ route('admin.search-suggestions.edit', $s) }}"
                     class="px-3 py-1 rounded-full border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                    {{ __('admin.edit') }}
                  </a>
                  <form method="POST" action="{{ route('admin.search-suggestions.destroy', $s) }}"
                        onsubmit="return confirm(@js(__('admin.confirm_delete_suggestion')))">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                      class="px-3 py-1 rounded-full border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50 transition">
                      {{ __('admin.delete') }}
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-6 text-center text-gray-500">{{ __('admin.no_suggestions') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-6">
    {{ $suggestions->links() }}
  </div>
@endsection
