<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
  <div>
    <h1 class="text-lg font-semibold">@yield('page_title', __('admin.dashboard'))</h1>
  </div>

  <div class="flex-1 lg:px-6">
    <div class="relative max-w-xl mx-auto">
      <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 rtl-search-icon">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="11" cy="11" r="7"></circle>
          <path d="M21 21l-4.3-4.3"></path>
        </svg>
      </span>
      <input
        id="global_search"
        type="text"
        placeholder="{{ __('admin.search') }}"
        data-no-results="{{ __('admin.no_results') }}"
        class="w-full h-11 rounded-full border border-gray-200 bg-white pl-11 pr-4 text-sm outline-none
               focus:border-red-300 focus:ring-4 focus:ring-red-100 transition rtl-search-input"
      >

      {{-- Suggestions --}}
      <div id="global_search_menu"
           class="hidden absolute z-50 mt-2 w-full rounded-2xl bg-white border border-gray-100 shadow-lg overflow-hidden">
        <div id="global_search_list" class="max-h-80 overflow-auto"></div>
      </div>
    </div>
  </div>

  <div class="flex items-center gap-3">
    {{-- Language Toggle --}}
    @php $nextLang = app()->getLocale() === 'ar' ? 'en' : 'ar'; @endphp
    <a href="{{ request()->fullUrlWithQuery(['lang' => $nextLang]) }}"
       class="h-11 px-4 rounded-full bg-white border border-gray-200 grid place-items-center text-sm font-semibold text-gray-700 shadow-sm hover:shadow-md transition">
      {{ strtoupper($nextLang) }}
    </a>

    {{-- Settings --}}
    <button type="button"
      class="w-11 h-11 rounded-full bg-white border border-gray-200 grid place-items-center
             shadow-sm hover:shadow-md transition">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-800" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M12 15.5A3.5 3.5 0 1 0 12 8.5a3.5 3.5 0 0 0 0 7z" />
        <path d="M19.4 15a7.9 7.9 0 0 0 .1-2l2-1.2-2-3.4-2.3.6a8 8 0 0 0-1.7-1L15 3h-6l-.5 3a8 8 0 0 0-1.7 1L4.5 6.4l-2 3.4 2 1.2a7.9 7.9 0 0 0 0 2l-2 1.2 2 3.4 2.3-.6a8 8 0 0 0 1.7 1L9 21h6l.5-3a8 8 0 0 0 1.7-1l2.3.6 2-3.4-2.1-1.2z" />
      </svg>
    </button>

    {{-- Notifications --}}
    <button type="button"
      class="w-11 h-11 rounded-full bg-white border border-gray-200 grid place-items-center
             shadow-sm hover:shadow-md transition relative">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-800" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7" />
        <path d="M13.73 21a2 2 0 0 1-3.46 0" />
      </svg>
      <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-red-600"></span>
    </button>
  </div>
</div>

<script>
  (function () {
    const input = document.getElementById('global_search');
    const menu = document.getElementById('global_search_menu');
    const list = document.getElementById('global_search_list');
    if (!input || !menu || !list) return;

    let t = null;
    const hide = () => menu.classList.add('hidden');
    const show = () => menu.classList.remove('hidden');

    function render(items) {
      if (!items || !items.length) {
        const emptyText = input.getAttribute('data-no-results') || 'No results';
        list.innerHTML = `<div class="px-4 py-3 text-sm text-gray-500">${emptyText}</div>`;
        show();
        return;
      }
      list.innerHTML = items.map(item => {
        const img = item.image
          ? `<img src="${item.image}" class="w-9 h-9 rounded-full object-cover" alt="">`
          : `<div class="w-9 h-9 rounded-full bg-gray-100 grid place-items-center text-gray-500 text-xs">${(item.type || '').slice(0,1)}</div>`;
        return `
          <a href="${item.url}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
            ${img}
            <div class="min-w-0">
              <div class="text-sm font-semibold text-gray-900 truncate">${item.label || ''}</div>
              <div class="text-xs text-gray-500 truncate">${item.type || ''}${item.sub ? ' • ' + item.sub : ''}</div>
            </div>
          </a>
        `;
      }).join('');
      show();
    }

    async function search(q) {
      if (!q || q.length < 2) {
        hide();
        return;
      }
      const url = new URL("{{ route('admin.search.suggest') }}", window.location.origin);
      url.searchParams.set('q', q);
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
      const data = await res.json();
      render(data.items || []);
    }

    input.addEventListener('input', function () {
      clearTimeout(t);
      t = setTimeout(() => search(this.value.trim()), 250);
    });

    document.addEventListener('click', function (e) {
      if (menu.contains(e.target) || input.contains(e.target)) return;
      hide();
    });
  })();
</script>
