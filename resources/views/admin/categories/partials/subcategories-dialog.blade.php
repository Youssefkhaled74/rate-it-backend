@php
  $dlgId = 'subs_' . $cat->id;
@endphp

<button type="button"
  onclick="document.getElementById('{{ $dlgId }}').showModal()"
  class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl bg-white border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
  Manage Subs
  <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">{{ $cat->subcategories_count }}</span>
</button>

<dialog id="{{ $dlgId }}" class="rounded-3xl p-0 w-[min(920px,95vw)] backdrop:bg-black/40">
  <div class="bg-white rounded-3xl overflow-hidden border border-gray-100">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
      <div>
        <div class="text-lg font-semibold text-gray-900">
          Subcategories • {{ $cat->name_en ?? $cat->name_ar }}
        </div>
        <div class="text-sm text-gray-500">Add & manage subcategories without leaving this page.</div>
      </div>

      <button type="button"
        onclick="document.getElementById('{{ $dlgId }}').close()"
        class="w-10 h-10 rounded-2xl bg-gray-50 border border-gray-100 grid place-items-center hover:bg-gray-100 transition"
        aria-label="Close">
        ✕
      </button>
    </div>

    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

      {{-- LEFT: List --}}
      <div>
        <div class="text-sm font-semibold text-gray-900 mb-3">Current subcategories</div>

        <div class="space-y-3 max-h-[60vh] overflow-auto pr-1">
          @forelse($cat->subcategories as $sub)
            @php
              $ready = false;
              try { $ready = $sub->isReadyForUse(); } catch(\Throwable $e) { $ready = false; }
            @endphp

            <div class="rounded-2xl border border-gray-100 bg-white p-4">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="font-semibold text-gray-900 truncate">
                    {{ $sub->name_en }} <span class="text-gray-400">•</span> {{ $sub->name_ar }}
                  </div>

                  <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
                    <span class="px-2 py-1 rounded-full {{ $sub->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                      {{ $sub->is_active ? 'Active' : 'Inactive' }}
                    </span>

                    <span class="px-2 py-1 rounded-full {{ $ready ? 'bg-amber-50 text-amber-800' : 'bg-red-50 text-red-700' }}">
                      {{ $ready ? 'Ready' : 'Not Ready' }}
                    </span>

                    <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                      Sort: {{ $sub->sort_order ?? 0 }}
                    </span>
                  </div>
                </div>

                {{-- Right actions --}}
                <div class="flex items-center gap-2 shrink-0">
                  <form method="POST" action="{{ route('admin.subcategories.toggle', $sub) }}">
                    @csrf
                    @method('PATCH')
                    <button class="px-3 py-2 rounded-xl bg-gray-50 border border-gray-100 text-xs font-semibold hover:bg-gray-100">
                      Toggle
                    </button>
                  </form>

                  <form method="POST" action="{{ route('admin.subcategories.destroy', $sub) }}"
                    onsubmit="return confirm('Delete subcategory?')">
                    @csrf
                    @method('DELETE')
                    <button class="px-3 py-2 rounded-xl bg-red-50 border border-red-100 text-xs font-semibold text-red-700 hover:bg-red-100">
                      Delete
                    </button>
                  </form>
                </div>
              </div>

              {{-- Inline edit --}}
              <form method="POST" action="{{ route('admin.subcategories.update', $sub) }}" enctype="multipart/form-data" class="mt-3 grid gap-3">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <input name="name_en" value="{{ old('name_en', $sub->name_en) }}" class="rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm focus:border-red-300 focus:ring-4 focus:ring-red-100 outline-none" placeholder="Name EN" required>
                  <input name="name_ar" value="{{ old('name_ar', $sub->name_ar) }}" class="rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm focus:border-red-300 focus:ring-4 focus:ring-red-100 outline-none" placeholder="Name AR" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
                  <input name="sort_order" value="{{ old('sort_order', $sub->sort_order) }}" type="number" min="0"
                    class="rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm focus:border-red-300 focus:ring-4 focus:ring-red-100 outline-none"
                    placeholder="Sort order">

                  <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $sub->is_active) ? 'checked' : '' }}>
                    Active
                  </label>

                  <input type="file" name="image" accept="image/*"
                    class="text-sm text-gray-600 file:mr-3 file:rounded-xl file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200">
                </div>

                <div class="flex justify-end">
                  <button class="px-4 py-2.5 rounded-2xl bg-red-900 text-white text-sm font-semibold hover:bg-red-950 transition">
                    Save
                  </button>
                </div>
              </form>
            </div>
          @empty
            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-6 text-center text-gray-500">
              No subcategories yet.
            </div>
          @endforelse
        </div>
      </div>

      {{-- RIGHT: Add --}}
      <div>
        <div class="text-sm font-semibold text-gray-900 mb-3">Add new subcategory</div>

        <form method="POST" action="{{ route('admin.categories.subcategories.store', $cat) }}" enctype="multipart/form-data"
          class="rounded-3xl border border-gray-100 bg-gray-50/40 p-5 space-y-4">
          @csrf

          <div>
            <label class="text-sm font-medium text-gray-700">Name (EN)</label>
            <input name="name_en" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-red-300 focus:ring-4 focus:ring-red-100" required>
          </div>

          <div>
            <label class="text-sm font-medium text-gray-700">Name (AR)</label>
            <input name="name_ar" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-red-300 focus:ring-4 focus:ring-red-100" required>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="text-sm font-medium text-gray-700">Sort Order</label>
              <input type="number" min="0" name="sort_order" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-red-300 focus:ring-4 focus:ring-red-100">
            </div>

            <div class="flex items-end">
              <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" checked>
                Active
              </label>
            </div>
          </div>

          <div>
            <label class="text-sm font-medium text-gray-700">Image (optional)</label>
            <input type="file" name="image" accept="image/*"
              class="mt-2 w-full text-sm text-gray-600 file:mr-3 file:rounded-xl file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200">
          </div>

          <button class="w-full rounded-2xl bg-red-900 text-white py-3 text-sm font-semibold hover:bg-red-950 transition">
            Add Subcategory
          </button>

          <div class="text-xs text-gray-500">
            Tip: “Ready” means it has rating criteria rules satisfied.
          </div>
        </form>
      </div>

    </div>
  </div>
</dialog>
