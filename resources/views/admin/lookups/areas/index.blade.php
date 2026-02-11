@extends('admin.layouts.app')

@section('title', __('admin.areas'))

@section('content')
<div class="space-y-6">

  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.areas') }}</h2>

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
          @if($status)
            <input type="hidden" name="status" value="{{ $status }}">
          @endif
          @if($cityId)
            <input type="hidden" name="city_id" value="{{ $cityId }}">
          @endif
        </form>
      </div>
      <div class="w-full max-w-xs">
        <form method="get">
          <select
            name="city_id"
            class="w-full rounded-2xl border border-gray-200 bg-white/80 px-4 py-3 text-sm outline-none
                   focus:border-red-300 focus:ring-4 focus:ring-red-100 transition"
            onchange="this.form.submit()"
          >
            <option value="">{{ __('admin.all') }} {{ __('admin.cities') }}</option>
            @foreach($cities as $city)
              <option value="{{ $city->id }}" {{ (string) $cityId === (string) $city->id ? 'selected' : '' }}>
                {{ $city->name_en }}
              </option>
            @endforeach
          </select>
          @if($q)
            <input type="hidden" name="q" value="{{ $q }}">
          @endif
          @if($status)
            <input type="hidden" name="status" value="{{ $status }}">
          @endif
        </form>
      </div>

      <a href="{{ route('admin.lookups.areas.template', ['lang' => request('lang')]) }}"
         class="inline-flex h-11 items-center justify-center gap-2 whitespace-nowrap rounded-xl border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-gray-50 hover:shadow">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <path d="M7 10l5 5 5-5"/>
          <path d="M12 15V3"/>
        </svg>
        <span>Download Template</span>
      </a>

      <form method="POST" action="{{ route('admin.lookups.areas.import', ['lang' => request('lang')]) }}" enctype="multipart/form-data" class="inline-flex">
        @csrf
        <input type="file" id="areas-import-file" name="file" accept=".xlsx,.xls,.csv" class="hidden" onchange="this.form.submit()">
        <button type="button" onclick="document.getElementById('areas-import-file').click()"
                class="inline-flex h-11 items-center justify-center gap-2 whitespace-nowrap rounded-xl border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-gray-50 hover:shadow">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <path d="M17 8l-5-5-5 5"/>
            <path d="M12 3v12"/>
          </svg>
          <span>Import Excel</span>
        </button>
      </form>

      <a href="{{ route('admin.lookups.areas.create', ['lang' => request('lang')]) }}"
         class="inline-flex h-11 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-red-900 px-5 text-sm font-semibold text-white shadow-soft transition hover:-translate-y-0.5 hover:bg-red-950">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 5v14M5 12h14"/>
        </svg>
        {{ __('admin.add_area') }}
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
    <a href="{{ route('admin.lookups.areas.index', ['lang' => request('lang'), 'city_id' => $cityId ?: null]) }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold {{ $status ? 'bg-white text-gray-600 border border-gray-100' : 'bg-red-900 text-white' }}">
      {{ __('admin.all') }}
    </a>
    <a href="{{ route('admin.lookups.areas.index', ['status' => 'active', 'lang' => request('lang'), 'city_id' => $cityId ?: null]) }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold {{ $status === 'active' ? 'bg-red-900 text-white' : 'bg-white text-gray-600 border border-gray-100' }}">
      {{ __('admin.active') }}
    </a>
    <a href="{{ route('admin.lookups.areas.index', ['status' => 'inactive', 'lang' => request('lang'), 'city_id' => $cityId ?: null]) }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold {{ $status === 'inactive' ? 'bg-red-900 text-white' : 'bg-white text-gray-600 border border-gray-100' }}">
      {{ __('admin.inactive') }}
    </a>
    @if($selectedCity)
      <span class="px-3 py-2 rounded-xl text-xs font-semibold bg-red-50 text-red-800 border border-red-100">
        {{ __('admin.city') }}: {{ $selectedCity->name_en }}
      </span>
    @endif
  </div>

  <div class="bg-white rounded-3xl shadow-soft p-6">
    <form id="bulk-delete-areas-form" method="POST" action="{{ route('admin.lookups.areas.bulk-destroy', ['lang' => request('lang')]) }}" class="mb-4 flex items-center justify-between gap-3">
      @csrf
      @method('DELETE')
      <div class="text-sm text-gray-600">
        <span id="areas-selected-count">0</span> selected
      </div>
      <button id="areas-bulk-delete-btn" type="submit" disabled
              onclick="return confirm('Delete selected areas?')"
              class="px-4 py-2 rounded-xl bg-red-900 text-white text-sm font-semibold disabled:opacity-40 disabled:cursor-not-allowed">
        {{ __('admin.delete') }}
      </button>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-gray-100">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50/70">
          <tr class="text-left text-gray-500">
            <th class="py-4 px-5 font-medium w-10">
              <input id="areas-select-all" type="checkbox" class="rounded border-gray-300">
            </th>
            <th class="py-4 px-5 font-medium">{{ __('admin.city') }}</th>
            <th class="py-4 px-5 font-medium">{{ __('admin.name_en') }}</th>
            <th class="py-4 px-5 font-medium">{{ __('admin.name_ar') }}</th>
            <th class="py-4 px-5 font-medium">{{ __('admin.status') }}</th>
            <th class="py-4 px-5 font-medium text-right">{{ __('admin.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
          @forelse($items as $item)
            <tr class="hover:bg-gray-50/60 transition">
              <td class="py-4 px-5">
                <input form="bulk-delete-areas-form" name="ids[]" value="{{ $item->id }}" type="checkbox" class="areas-row-checkbox rounded border-gray-300">
              </td>
              <td class="py-4 px-5 text-gray-900 font-semibold">{{ $item->city?->name_en ?? '-' }}</td>
              <td class="py-4 px-5 text-gray-700">{{ $item->name_en }}</td>
              <td class="py-4 px-5 text-gray-700">{{ $item->name_ar ?? '-' }}</td>
              <td class="py-4 px-5">
                <form method="POST" action="{{ route('admin.lookups.areas.toggle', $item) }}">
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
                  <a href="{{ route('admin.lookups.areas.edit', $item) }}"
                     class="px-4 py-2 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold hover:bg-gray-200 transition">
                    {{ __('admin.edit') }}
                  </a>
                  <form method="POST" action="{{ route('admin.lookups.areas.destroy', $item) }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" data-confirm="delete-area-{{ $item->id }}" data-confirm-text="{{ __('admin.delete') }}" data-title="{{ __('admin.delete') }}" data-message="{{ __('admin.confirm_message') }}"
                      class="px-4 py-2 rounded-full bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100 transition">
                      {{ __('admin.delete') }}
                    </button>
                    <input type="hidden" name="_confirm_target" value="delete-area-{{ $item->id }}" />
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-12 text-center text-gray-500">{{ __('admin.no_data') }}</td>
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

<script>
  (function () {
    const selectAll = document.getElementById('areas-select-all');
    const checkboxes = Array.from(document.querySelectorAll('.areas-row-checkbox'));
    const countEl = document.getElementById('areas-selected-count');
    const btn = document.getElementById('areas-bulk-delete-btn');

    function sync() {
      const checked = checkboxes.filter(cb => cb.checked).length;
      countEl.textContent = checked;
      btn.disabled = checked === 0;
      if (selectAll) {
        selectAll.checked = checked > 0 && checked === checkboxes.length;
        selectAll.indeterminate = checked > 0 && checked < checkboxes.length;
      }
    }

    if (selectAll) {
      selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
        sync();
      });
    }
    checkboxes.forEach(cb => cb.addEventListener('change', sync));
    sync();
  })();
</script>
@endsection
