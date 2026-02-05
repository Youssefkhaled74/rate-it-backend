@php
  $isEdit = !empty($brand);
  $logoUrl = $isEdit && !empty($brand->logo)
    ? asset($brand->logo)
    : asset('assets/images/category-icon-placeholder.png');
  $coverUrl = $isEdit && !empty($brand->cover_image)
    ? asset($brand->cover_image)
    : asset('assets/images/category-placeholder.png');
@endphp

<div class="space-y-6">

  <div>
    <label class="text-sm font-medium text-gray-700">{{ __('admin.brand_name_en') }}</label>
    <input
      name="name_en"
      value="{{ old('name_en', $brand->name_en ?? '') }}"
      placeholder="{{ __('admin.brand_name_placeholder') }}"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
      required
    >
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">{{ __('admin.brand_name_ar') }}</label>
    <input
      name="name_ar"
      value="{{ old('name_ar', $brand->name_ar ?? '') }}"
      placeholder="{{ __('admin.brand_name_ar_placeholder') }}"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
    >
  </div>

  <x-admin.select
    name="subcategory_id"
    label="{{ __('admin.subcategory') }}"
    placeholder="{{ __('admin.choose_subcategory') }}"
  >
    @foreach($subcategories ?? [] as $sub)
      <option value="{{ $sub->id }}" {{ (string) old('subcategory_id', $brand->subcategory_id ?? '') === (string) $sub->id ? 'selected' : '' }}>
        {{ $sub->name_en ?? $sub->name_ar ?? __('admin.subcategory') }}
      </option>
    @endforeach
  </x-admin.select>

  <div>
    <label class="text-sm font-medium text-gray-700">{{ __('admin.description_en') }}</label>
    <textarea
      name="description_en"
      rows="3"
      placeholder="Short description"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
    >{{ old('description_en', $brand->description_en ?? '') }}</textarea>
  </div>

  <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-4">
    <div class="text-sm font-semibold text-gray-900 mb-3">{{ __('admin.contract_details') }}</div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="text-sm font-medium text-gray-700">{{ __('admin.start_date') }}</label>
        <input
          type="date"
          name="start_date"
          value="{{ old('start_date', optional($brand->start_date ?? null)->format('Y-m-d')) }}"
          class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                 focus:border-red-300 focus:ring-4 focus:ring-red-100"
        >
      </div>
      <div>
        <label class="text-sm font-medium text-gray-700">{{ __('admin.end_date') }}</label>
        <input
          type="date"
          name="end_date"
          value="{{ old('end_date', optional($brand->end_date ?? null)->format('Y-m-d')) }}"
          class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                 focus:border-red-300 focus:ring-4 focus:ring-red-100"
        >
      </div>
    </div>
  </div>

  <div>
    <label class="text-sm font-medium text-gray-700">{{ __('admin.description_ar') }}</label>
    <textarea
      name="description_ar"
      rows="3"
      placeholder="Short description (Arabic)"
      class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
             focus:border-red-300 focus:ring-4 focus:ring-red-100"
    >{{ old('description_ar', $brand->description_ar ?? '') }}</textarea>
  </div>

  <div>
    <div class="text-sm font-medium text-gray-700">Brand Images</div>

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
            <div class="text-sm font-semibold text-gray-800">Add Brand Logo</div>
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
             {{ old('is_active', $brand->is_active ?? 1) ? 'checked' : '' }}
             class="sr-only peer">

      <span class="w-12 h-6 rounded-full bg-gray-200 peer-checked:bg-green-500 transition relative">
        <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white transition peer-checked:translate-x-6"></span>
      </span>

      <span class="text-sm text-gray-700">
        <span class="font-semibold">{{ __('admin.active') }}</span>
        <span class="text-gray-500">— {{ __('admin.visible_in_app') }}</span>
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
