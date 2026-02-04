@extends('admin.layouts.app')

@section('title', __('admin.genders'))

@section('content')
<div class="space-y-6">

  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.genders') }}</h2>

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
            value="{{ $q }}"
            placeholder="{{ __('admin.search') }}"
            class="w-full rounded-2xl border border-gray-200 bg-white/80 pl-11 pr-4 py-3 text-sm outline-none rtl-search-input
                   focus:border-red-300 focus:ring-4 focus:ring-red-100 transition"
          >
        </form>
      </div>

      <a href="{{ route('admin.lookups.genders.create', ['lang' => request('lang')]) }}"
         class="inline-flex items-center justify-center rounded-2xl bg-red-900 text-white px-5 py-3 text-sm font-semibold shadow-soft hover:bg-red-950 transition">
        {{ __('admin.add_gender') }}
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-[22px] bg-red-900 text-white p-5 shadow-soft">
      <div class="text-sm opacity-90">{{ __('admin.total') }}</div>
      <div class="mt-2 text-3xl font-semibold">{{ $total }}</div>
    </div>
    <div class="rounded-[22px] bg-white p-5 border border-gray-100 shadow-soft">
      <div class="text-sm text-gray-600">{{ __('admin.active') }}</div>
      <div class="mt-2 text-3xl font-semibold text-red-900">{{ $active }}</div>
    </div>
    <div class="rounded-[22px] bg-white p-5 border border-gray-100 shadow-soft">
      <div class="text-sm text-gray-600">{{ __('admin.inactive') }}</div>
      <div class="mt-2 text-3xl font-semibold text-red-900">{{ $inactive }}</div>
    </div>
  </div>

  <div class="flex items-center gap-3">
    <a href="{{ route('admin.lookups.genders.index', ['lang' => request('lang')]) }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold {{ $status ? 'bg-white text-gray-600 border border-gray-100' : 'bg-red-900 text-white' }}">
      {{ __('admin.all') }}
    </a>
    <a href="{{ route('admin.lookups.genders.index', ['status' => 'active', 'lang' => request('lang')]) }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold {{ $status === 'active' ? 'bg-red-900 text-white' : 'bg-white text-gray-600 border border-gray-100' }}">
      {{ __('admin.active') }}
    </a>
    <a href="{{ route('admin.lookups.genders.index', ['status' => 'inactive', 'lang' => request('lang')]) }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold {{ $status === 'inactive' ? 'bg-red-900 text-white' : 'bg-white text-gray-600 border border-gray-100' }}">
      {{ __('admin.inactive') }}
    </a>
  </div>

  <div class="bg-white rounded-3xl shadow-soft p-6">
    <div class="overflow-x-auto rounded-2xl border border-gray-100">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50/70">
          <tr class="text-left text-gray-500">
            <th class="py-4 px-5 font-medium">{{ __('admin.code') }}</th>
            <th class="py-4 px-5 font-medium">{{ __('admin.name_en') }}</th>
            <th class="py-4 px-5 font-medium">{{ __('admin.name_ar') }}</th>
            <th class="py-4 px-5 font-medium">{{ __('admin.status') }}</th>
            <th class="py-4 px-5 font-medium text-right">{{ __('admin.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
          @forelse($items as $item)
            <tr class="hover:bg-gray-50/60 transition">
              <td class="py-4 px-5 text-gray-900 font-semibold">{{ $item->code }}</td>
              <td class="py-4 px-5 text-gray-700">{{ $item->name_en }}</td>
              <td class="py-4 px-5 text-gray-700">{{ $item->name_ar ?? '-' }}</td>
              <td class="py-4 px-5">
                <form method="POST" action="{{ route('admin.lookups.genders.toggle', $item) }}">
                  @csrf
                  @method('PATCH')
                  <button type="submit"
                    class="w-10 h-6 rounded-full {{ $item->is_active ? 'bg-red-900' : 'bg-gray-200' }} relative transition">
                    <span class="absolute top-0.5 {{ $item->is_active ? 'left-5' : 'left-0.5' }} w-5 h-5 rounded-full bg-white transition"></span>
                  </button>
                </form>
              </td>
              <td class="py-4 px-5 text-right">
                <div class="inline-flex items-center gap-2">
                  <a href="{{ route('admin.lookups.genders.edit', $item) }}"
                     class="px-4 py-2 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold hover:bg-gray-200 transition">
                    {{ __('admin.edit') }}
                  </a>
                  <form method="POST" action="{{ route('admin.lookups.genders.destroy', $item) }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" data-confirm="delete-gender-{{ $item->id }}" data-confirm-text="{{ __('admin.delete') }}" data-title="{{ __('admin.delete') }}" data-message="{{ __('admin.confirm_message') }}"
                      class="px-4 py-2 rounded-full bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100 transition">
                      {{ __('admin.delete') }}
                    </button>
                    <input type="hidden" name="_confirm_target" value="delete-gender-{{ $item->id }}" />
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-12 text-center text-gray-500">{{ __('admin.no_data') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-5">
      {{ $items->links() }}
    </div>
  </div>
</div>
@endsection
