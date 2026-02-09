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
    <div>
      <label class="text-sm font-medium text-gray-700">Branch Logo</label>
      <input
        type="file"
        name="logo"
        accept="image/*"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
      @if(!empty($branch?->logo))
        <div class="mt-3 flex items-center gap-3">
          <img src="{{ asset($branch->logo) }}" alt="Logo" class="w-12 h-12 rounded-xl object-cover border border-gray-100">
          <div class="text-xs text-gray-500">Current logo</div>
        </div>
      @endif
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">Cover Image</label>
      <input
        type="file"
        name="cover_image"
        accept="image/*"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
      @if(!empty($branch?->cover_image))
        <div class="mt-3">
          <img src="{{ asset($branch->cover_image) }}" alt="Cover" class="w-full max-w-sm h-28 rounded-2xl object-cover border border-gray-100">
        </div>
      @endif
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
        id="branch_lat"
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
        id="branch_lng"
        name="lng"
        value="{{ old('lng', $branch->lng ?? '') }}"
        placeholder="e.g. 31.1376"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
  </div>

  <div>
    <div class="flex items-center justify-between">
      <label class="text-sm font-medium text-gray-700">Pick Location on Map</label>
      <button type="button" id="map_use_current"
              class="text-xs font-semibold text-red-800 hover:text-red-900">
        Use current location
      </button>
    </div>

    <div class="mt-3 relative">
      <input
        id="map_search"
        type="text"
        placeholder="Search address or place"
        class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
      <div id="map_search_results"
           class="hidden absolute z-10 mt-2 w-full rounded-2xl bg-white border border-gray-100 shadow-lg overflow-hidden max-h-56 overflow-auto"></div>
    </div>

    <div id="branch_map" class="mt-3 w-full h-64 rounded-3xl border border-gray-200 overflow-hidden"></div>
    <div class="text-xs text-gray-500 mt-2">
      Click on the map to set the location. Latitude & Longitude will update automatically.
    </div>
  </div>

  <div>
    <div class="flex items-center justify-between">
      <label class="text-sm font-medium text-gray-700">Working Hours</label>
      <button type="button" id="wh_toggle_json"
              class="text-xs font-semibold text-red-800 hover:text-red-900">
        Show JSON
      </button>
    </div>

    <div class="mt-3 rounded-2xl border border-gray-200 bg-white p-4 space-y-3" id="wh_builder">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
        <div class="text-xs font-semibold text-gray-500">Day</div>
        <div class="text-xs font-semibold text-gray-500">Open</div>
        <div class="text-xs font-semibold text-gray-500">Close</div>
      </div>

      @php
        $days = [
          'sat' => 'Saturday',
          'sun' => 'Sunday',
          'mon' => 'Monday',
          'tue' => 'Tuesday',
          'wed' => 'Wednesday',
          'thu' => 'Thursday',
          'fri' => 'Friday',
        ];
      @endphp

      @foreach($days as $key => $label)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
          <div class="text-sm text-gray-700 font-semibold">{{ $label }}</div>
          <input type="time"
                 data-wh-day="{{ $key }}"
                 data-wh-field="open"
                 class="rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm outline-none transition
                        focus:border-red-300 focus:ring-4 focus:ring-red-100">
          <input type="time"
                 data-wh-day="{{ $key }}"
                 data-wh-field="close"
                 class="rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm outline-none transition
                        focus:border-red-300 focus:ring-4 focus:ring-red-100">
        </div>
      @endforeach

      <div class="flex items-center gap-3 pt-2">
        <button type="button" id="wh_apply_weekdays"
                class="text-xs font-semibold text-gray-700 hover:text-gray-900">
          Copy Monday to all weekdays
        </button>
        <span class="text-xs text-gray-400">|</span>
        <button type="button" id="wh_clear"
                class="text-xs font-semibold text-red-700 hover:text-red-900">
          Clear all
        </button>
      </div>
    </div>

    <div class="mt-3 hidden" id="wh_json_wrap">
      <textarea
        id="wh_json"
        name="working_hours"
        rows="5"
        placeholder='{"sat":{"open":"09:00","close":"22:00"}}'
        class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-mono outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >{{ old('working_hours', $workingHoursValue) }}</textarea>
      <div class="text-xs text-gray-500 mt-2">If you edit JSON directly, it must be valid.</div>
    </div>
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

@push('styles')
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@push('scripts')
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
          integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script>
    (function () {
      const latInput = document.getElementById('branch_lat');
      const lngInput = document.getElementById('branch_lng');
      const mapEl = document.getElementById('branch_map');
      const searchInput = document.getElementById('map_search');
      const searchResults = document.getElementById('map_search_results');
      if (!latInput || !lngInput || !mapEl || !window.L) return;

      const toNum = (v) => {
        const n = parseFloat(v);
        return Number.isFinite(n) ? n : null;
      };

      const initialLat = toNum(latInput.value) ?? 30.0444; // Cairo fallback
      const initialLng = toNum(lngInput.value) ?? 31.2357;

      const map = L.map(mapEl).setView([initialLat, initialLng], 12);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);

      const marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

      function setLatLng(lat, lng) {
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
        marker.setLatLng([lat, lng]);
      }

      map.on('click', function (e) {
        setLatLng(e.latlng.lat, e.latlng.lng);
      });

      marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        setLatLng(pos.lat, pos.lng);
      });

      const btn = document.getElementById('map_use_current');
      if (btn && navigator.geolocation) {
        btn.addEventListener('click', function () {
          navigator.geolocation.getCurrentPosition(function (pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            map.setView([lat, lng], 15);
            setLatLng(lat, lng);
          });
        });
      } else if (btn) {
        btn.classList.add('hidden');
      }

      function hideResults() {
        if (searchResults) searchResults.classList.add('hidden');
      }

      function showResults() {
        if (searchResults) searchResults.classList.remove('hidden');
      }

      if (searchInput && searchResults) {
        let t = null;
        searchInput.addEventListener('input', function () {
          clearTimeout(t);
          const q = this.value.trim();
          if (q.length < 3) {
            hideResults();
            searchResults.innerHTML = '';
            return;
          }
          t = setTimeout(async () => {
            try {
              const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&addressdetails=1&limit=6`;
              const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
              const data = await res.json();
              if (!Array.isArray(data) || data.length === 0) {
                searchResults.innerHTML = `<div class="px-4 py-3 text-sm text-gray-500">No results</div>`;
                showResults();
                return;
              }
              searchResults.innerHTML = data.map(item => {
                return `
                  <button type="button" class="w-full text-left px-4 py-3 text-sm hover:bg-gray-50" data-lat="${item.lat}" data-lng="${item.lon}">
                    ${item.display_name}
                  </button>
                `;
              }).join('');
              showResults();
            } catch (e) {
              hideResults();
            }
          }, 300);
        });

        searchResults.addEventListener('click', function (e) {
          const btn = e.target.closest('button[data-lat][data-lng]');
          if (!btn) return;
          const lat = parseFloat(btn.getAttribute('data-lat'));
          const lng = parseFloat(btn.getAttribute('data-lng'));
          if (Number.isFinite(lat) && Number.isFinite(lng)) {
            map.setView([lat, lng], 16);
            setLatLng(lat, lng);
          }
          hideResults();
        });

        document.addEventListener('click', function (e) {
          if (searchResults.contains(e.target) || searchInput.contains(e.target)) return;
          hideResults();
        });
      }
    })();
  </script>

  <script>
    (function () {
      const jsonToggle = document.getElementById('wh_toggle_json');
      const jsonWrap = document.getElementById('wh_json_wrap');
      const jsonInput = document.getElementById('wh_json');
      const builder = document.getElementById('wh_builder');
      const dayInputs = document.querySelectorAll('[data-wh-day][data-wh-field]');
      const btnCopy = document.getElementById('wh_apply_weekdays');
      const btnClear = document.getElementById('wh_clear');

      function buildJsonFromInputs() {
        const data = {};
        dayInputs.forEach(input => {
          const day = input.getAttribute('data-wh-day');
          const field = input.getAttribute('data-wh-field');
          const val = (input.value || '').trim();
          if (!val) return;
          data[day] = data[day] || {};
          data[day][field] = val;
        });
        jsonInput.value = Object.keys(data).length ? JSON.stringify(data) : '';
      }

      function fillInputsFromJson() {
        if (!jsonInput.value.trim()) return;
        try {
          const obj = JSON.parse(jsonInput.value);
          dayInputs.forEach(input => {
            const day = input.getAttribute('data-wh-day');
            const field = input.getAttribute('data-wh-field');
            const val = obj?.[day]?.[field] || '';
            input.value = val;
          });
        } catch (e) {
          // keep silent if JSON invalid
        }
      }

      dayInputs.forEach(input => {
        input.addEventListener('change', buildJsonFromInputs);
        input.addEventListener('input', buildJsonFromInputs);
      });

      if (btnCopy) {
        btnCopy.addEventListener('click', function () {
          const mondayOpen = document.querySelector('[data-wh-day="mon"][data-wh-field="open"]')?.value || '';
          const mondayClose = document.querySelector('[data-wh-day="mon"][data-wh-field="close"]')?.value || '';
          ['mon','tue','wed','thu','fri'].forEach(day => {
            const o = document.querySelector(`[data-wh-day="${day}"][data-wh-field="open"]`);
            const c = document.querySelector(`[data-wh-day="${day}"][data-wh-field="close"]`);
            if (o) o.value = mondayOpen;
            if (c) c.value = mondayClose;
          });
          buildJsonFromInputs();
        });
      }

      if (btnClear) {
        btnClear.addEventListener('click', function () {
          dayInputs.forEach(i => i.value = '');
          jsonInput.value = '';
        });
      }

      if (jsonToggle && jsonWrap && builder) {
        jsonToggle.addEventListener('click', function () {
          const isHidden = jsonWrap.classList.contains('hidden');
          if (isHidden) {
            jsonWrap.classList.remove('hidden');
            jsonToggle.textContent = 'Hide JSON';
          } else {
            jsonWrap.classList.add('hidden');
            jsonToggle.textContent = 'Show JSON';
          }
        });
      }

      fillInputsFromJson();
      buildJsonFromInputs();
    })();
  </script>
@endpush
