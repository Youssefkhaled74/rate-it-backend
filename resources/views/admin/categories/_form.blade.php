@php
  $isEdit = !empty($category);
  $logoUrl = $isEdit && !empty($category->logo)
    ? asset($category->logo)
    : asset('assets/images/category-placeholder.png');
@endphp

<div class="space-y-6">

  {{-- Category name --}}
  <div>
    <label class="text-sm font-medium text-gray-700">Category Name (EN)</label>
    <input
      name="name_en"
      value="{{ old('name_en', $category->name_en ?? '') }}"
      placeholder="Category Name"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
      required
    >
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Category Name (AR)</label>
    <input
      name="name_ar"
      value="{{ old('name_ar', $category->name_ar ?? '') }}"
      placeholder="اسم التصنيف"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
    >
  </div>

  {{-- Uploads --}}
  <div>
    <div class="text-sm font-medium text-gray-700">Category Image & Icon</div>

    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- Image box --}}
        <div>
          <input id="logo_input" type="file" name="logo" accept="image/*" class="hidden">
          <label for="logo_input"
            class="block rounded-[26px] border-2 border-dashed border-gray-200 bg-white hover:border-gray-300 transition
                   p-5 cursor-pointer min-h-[170px]">
            <div class="h-full flex flex-col items-center justify-center text-center gap-3">
              <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 grid place-items-center text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M21 15V8a2 2 0 0 0-2-2h-3l-2-2H8L6 6H5a2 2 0 0 0-2 2v7"/>
                  <path d="M3 15l4-4a2 2 0 0 1 3 0l1 1"/>
                  <path d="M14 13l1-1a2 2 0 0 1 3 0l3 3"/>
                </svg>
              </div>
              <div class="text-sm font-semibold text-gray-800">Add Category Image</div>
              <div class="text-xs text-gray-500">PNG/JPG/WEBP up to 4MB</div>
            </div>
          </label>

          {{-- Preview --}}
          <div class="mt-3 rounded-2xl overflow-hidden border border-gray-100 bg-gray-50">
            <img id="logo_preview" src="{{ $logoUrl }}" class="w-full h-36 object-cover" alt="preview">
          </div>
        </div>

        {{-- Icon box --}}
        <div>
          <input id="icon_input" type="file" name="icon" accept="image/*" class="hidden">
          <label for="icon_input"
            class="block rounded-[26px] border-2 border-dashed border-gray-200 bg-white hover:border-gray-300 transition
                   p-5 cursor-pointer min-h-[170px]">
            <div class="h-full flex flex-col items-center justify-center text-center gap-3">
              <div class="w-12 h-12 rounded-full bg-gray-50 border border-gray-100 grid place-items-center text-gray-500 overflow-hidden">
                <img id="icon_preview_img" src="{{ $isEdit && !empty($category->icon) ? asset($category->icon) : asset('assets/images/category-icon-placeholder.png') }}" class="w-10 h-10 object-cover rounded-full" alt="icon preview">
              </div>
              <div class="text-sm font-semibold text-gray-800">Add Category Icon</div>
              <div class="text-xs text-gray-500">PNG/JPG/WEBP up to 2MB</div>
            </div>
          </label>

          {{-- Small preview caption --}}
          <div class="mt-3 text-sm text-gray-500">Icon appears as a small rounded image next to category name.</div>
        </div>

      </div>
  </div>

  {{-- Access + sort --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

    <div>
      <label class="text-sm font-medium text-gray-700">Sort Order (optional)</label>
      <input
        name="sort_order"
        type="number"
        value="{{ old('sort_order', $category->sort_order ?? '') }}"
        placeholder="0"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>

    <div class="flex items-end">
      <label class="inline-flex items-center gap-3 cursor-pointer select-none">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $category->is_active ?? 1) ? 'checked' : '' }}
               class="sr-only peer">

        <span class="w-12 h-6 rounded-full bg-gray-200 peer-checked:bg-green-500 transition relative">
          <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white transition peer-checked:translate-x-6"></span>
        </span>

        <span class="text-sm text-gray-700">
          <span class="font-semibold">Active</span>
          <span class="text-gray-500">— visible in app</span>
        </span>
      </label>
    </div>

  </div>

</div>

<script>
  (function(){
    const logoInput = document.getElementById('logo_input');
    const logoPreview = document.getElementById('logo_preview');
    const iconInput = document.getElementById('icon_input');
    const iconPreviewImg = document.getElementById('icon_preview_img');

    if (logoInput && logoPreview) {
      logoInput.addEventListener('change', function(){
        const f = this.files && this.files[0];
        if(!f) return;
        const url = URL.createObjectURL(f);
        logoPreview.src = url;
      });
    }

    if (iconInput && iconPreviewImg) {
      iconInput.addEventListener('change', function(){
        const f = this.files && this.files[0];
        if(!f) return;
        const url = URL.createObjectURL(f);
        iconPreviewImg.src = url;
      });
    }
  })();
</script>
