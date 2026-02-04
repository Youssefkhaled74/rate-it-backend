@php
  $isEdit = !empty($branch);
  $workingHoursValue = $isEdit && !empty($branch->working_hours)
    ? json_encode($branch->working_hours, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    : '';
@endphp

<div class="space-y-6">

  <x-admin.select
    name="place_id"
    label="Place"
    placeholder="Choose place"
    :required="true"
  >
    @foreach($places as $place)
      <option value="{{ $place->id }}" {{ (string) old('place_id', $branch->place_id ?? '') === (string) $place->id ? 'selected' : '' }}>
        {{ $place->display_name }}
      </option>
    @endforeach
  </x-admin.select>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">Branch Name (optional)</label>
      <input
        name="name"
        value="{{ old('name', $branch->name ?? '') }}"
        placeholder="Branch name"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">Review Cooldown (days)</label>
      <input
        name="review_cooldown_days"
        type="number"
        min="0"
        value="{{ old('review_cooldown_days', $branch->review_cooldown_days ?? 0) }}"
        placeholder="0"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <x-admin.select
      name="city_id"
      label="City"
      placeholder="Choose city"
    >
      @foreach($cities ?? [] as $city)
        <option value="{{ $city->id }}" {{ (string) old('city_id', $branch->city_id ?? '') === (string) $city->id ? 'selected' : '' }}>
          {{ $city->name_en }}
        </option>
      @endforeach
    </x-admin.select>
    <x-admin.select
      name="area_id"
      label="Area"
      placeholder="Choose area"
    >
      @foreach($areas ?? [] as $area)
        <option value="{{ $area->id }}" {{ (string) old('area_id', $branch->area_id ?? '') === (string) $area->id ? 'selected' : '' }}>
          {{ $area->name_en }}
        </option>
      @endforeach
    </x-admin.select>
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Address</label>
    <textarea
      name="address"
      rows="3"
      placeholder="Branch address"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
      required
    >{{ old('address', $branch->address ?? '') }}</textarea>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">Latitude</label>
      <input
        name="lat"
        value="{{ old('lat', $branch->lat ?? '') }}"
        placeholder="e.g. 29.9753"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">Longitude</label>
      <input
        name="lng"
        value="{{ old('lng', $branch->lng ?? '') }}"
        placeholder="e.g. 31.1376"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Working Hours (JSON)</label>
    <textarea
      name="working_hours"
      rows="5"
      placeholder='{"sat": {"open": "09:00", "close": "22:00"}}'
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-mono outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
    >{{ old('working_hours', $workingHoursValue) }}</textarea>
    <div class="text-xs text-gray-500 mt-2">Leave empty to keep it blank. JSON must be valid.</div>
  </div>

  <div class="flex items-end">
    <label class="inline-flex items-center gap-3 cursor-pointer select-none">
      <input type="checkbox" name="is_active" value="1"
             {{ old('is_active', $branch->is_active ?? 1) ? 'checked' : '' }}
             class="sr-only peer">

      <span class="w-12 h-6 rounded-full bg-gray-200 peer-checked:bg-green-500 transition relative">
        <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white transition peer-checked:translate-x-6"></span>
      </span>

      <span class="text-sm text-gray-700">
        <span class="font-semibold">Active</span>
        <span class="text-gray-500">- visible in app</span>
      </span>
    </label>
  </div>
</div>
