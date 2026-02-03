@php
  $isEdit = !empty($place);
  $logoUrl = $isEdit && !empty($place->logo)
    ? asset($place->logo)
    : asset('assets/images/category-icon-placeholder.png');
  $coverUrl = $isEdit && !empty($place->cover_image)
    ? asset($place->cover_image)
    : asset('assets/images/category-placeholder.png');
@endphp

<div class="space-y-6">

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">Brand</label>
      <select name="brand_id" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                    focus:border-red-300 focus:ring-4 focus:ring-red-100">
        <option value="">None</option>
        @foreach($brands as $brand)
          <option value="{{ $brand->id }}" {{ (string) old('brand_id', $place->brand_id ?? '') === (string) $brand->id ? 'selected' : '' }}>
            {{ $brand->name_en }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-sm font-medium text-gray-700">Subcategory</label>
      <select name="subcategory_id" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                    focus:border-red-300 focus:ring-4 focus:ring-red-100">
        <option value="">None</option>
        @foreach($subcategories as $sub)
          <option value="{{ $sub->id }}" {{ (string) old('subcategory_id', $place->subcategory_id ?? '') === (string) $sub->id ? 'selected' : '' }}>
            {{ $sub->name_en }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Place Name (EN)</label>
    <input
      name="name_en"
      value="{{ old('name_en', $place->name_en ?? '') }}"
      placeholder="Place Name"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
      required
    >
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Place Name (AR)</label>
    <input
      name="name_ar"
      value="{{ old('name_ar', $place->name_ar ?? '') }}"
      placeholder="Place Name (Arabic)"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
    >
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-medium text-gray-700">Title (EN)</label>
      <input
        name="title_en"
        value="{{ old('title_en', $place->title_en ?? '') }}"
        placeholder="Optional short title"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
    <div>
      <label class="text-sm font-medium text-gray-700">Title (AR)</label>
      <input
        name="title_ar"
        value="{{ old('title_ar', $place->title_ar ?? '') }}"
        placeholder="Optional short title (Arabic)"
        class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
               focus:border-red-300 focus:ring-4 focus:ring-red-100"
      >
    </div>
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Description (EN)</label>
    <textarea
      name="description_en"
      rows="3"
      placeholder="Short description"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
    >{{ old('description_en', $place->description_en ?? '') }}</textarea>
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">Description (AR)</label>
    <textarea
      name="description_ar"
      rows="3"
      placeholder="Short description (Arabic)"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
    >{{ old('description_ar', $place->description_ar ?? '') }}</textarea>
  </div>

  <div>
    <div class="text-sm font-medium text-gray-700">Place Images</div>

    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4">

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
            <div class="text-sm font-semibold text-gray-800">Add Place Logo</div>
            <div class="text-xs text-gray-500">PNG/JPG/WEBP up to 4MB</div>
          </div>
        </label>

        <div class="mt-3 rounded-2xl overflow-hidden border border-gray-100 bg-gray-50">
          <img id="logo_preview" src="{{ $logoUrl }}" class="w-full h-36 object-cover" alt="logo preview">
        </div>
      </div>

      <div>
        <input id="cover_input" type="file" name="cover_image" accept="image/*" class="hidden">
        <label for="cover_input"
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
            <div class="text-sm font-semibold text-gray-800">Add Cover Image</div>
            <div class="text-xs text-gray-500">PNG/JPG/WEBP up to 6MB</div>
          </div>
        </label>

        <div class="mt-3 rounded-2xl overflow-hidden border border-gray-100 bg-gray-50">
          <img id="cover_preview" src="{{ $coverUrl }}" class="w-full h-36 object-cover" alt="cover preview">
        </div>
      </div>

    </div>
  </div>

  <div class="flex items-end">
    <label class="inline-flex items-center gap-3 cursor-pointer select-none">
      <input type="checkbox" name="is_active" value="1"
             {{ old('is_active', $place->is_active ?? 1) ? 'checked' : '' }}
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

<script>
  (function(){
    const logoInput = document.getElementById('logo_input');
    const logoPreview = document.getElementById('logo_preview');
    const coverInput = document.getElementById('cover_input');
    const coverPreview = document.getElementById('cover_preview');

    if (logoInput && logoPreview) {
      logoInput.addEventListener('change', function(){
        const f = this.files && this.files[0];
        if(!f) return;
        const url = URL.createObjectURL(f);
        logoPreview.src = url;
      });
    }

    if (coverInput && coverPreview) {
      coverInput.addEventListener('change', function(){
        const f = this.files && this.files[0];
        if(!f) return;
        const url = URL.createObjectURL(f);
        coverPreview.src = url;
      });
    }
  })();
</script>
