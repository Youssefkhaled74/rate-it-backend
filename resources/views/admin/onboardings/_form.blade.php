@php
  $isEdit = !empty($onboarding);
  $imgUrl = $isEdit && !empty($onboarding->image)
    ? asset($onboarding->image)
    : asset('assets/images/category-placeholder.png');
@endphp

<div class="space-y-6">

  <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-center">
    <div class="lg:col-span-2">
      <input id="onboarding_image" type="file" name="image" accept="image/*" class="hidden">
      <label for="onboarding_image"
        class="block rounded-[26px] border-2 border-dashed border-gray-200 bg-white hover:border-gray-300 transition
               p-6 cursor-pointer min-h-[190px]">
        <div class="h-full flex flex-col items-center justify-center text-center gap-3">
          <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 grid place-items-center text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M21 15V8a2 2 0 0 0-2-2h-3l-2-2H8L6 6H5a2 2 0 0 0-2 2v7"/>
              <path d="M3 15l4-4a2 2 0 0 1 3 0l1 1"/>
              <path d="M14 13l1-1a2 2 0 0 1 3 0l3 3"/>
            </svg>
          </div>
          <div class="text-sm font-semibold text-gray-800">Add Onboarding Image</div>
          <div class="text-xs text-gray-500">PNG/JPG/WEBP up to 6MB</div>
        </div>
      </label>
      <div class="mt-3 rounded-2xl overflow-hidden border border-gray-100 bg-gray-50">
        <img id="onboarding_preview" src="{{ $imgUrl }}" class="w-full h-36 object-cover" alt="onboarding preview">
      </div>
    </div>

    <div class="lg:col-span-3 space-y-4">
      <div>
        <label class="text-sm font-medium text-gray-700">Title</label>
        <input
          name="title"
          value="{{ old('title', $onboarding->title ?? '') }}"
          placeholder="Title"
          class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                 focus:border-red-300 focus:ring-4 focus:ring-red-100"
          required
        >
      </div>

      <div>
        <label class="text-sm font-medium text-gray-700">Subtitle</label>
        <input
          name="subtitle"
          value="{{ old('subtitle', $onboarding->subtitle ?? '') }}"
          placeholder="Subtitle"
          class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition
                 focus:border-red-300 focus:ring-4 focus:ring-red-100"
        >
      </div>

      <div class="flex items-end">
        <label class="inline-flex items-center gap-3 cursor-pointer select-none">
          <input type="checkbox" name="is_active" value="1"
                 {{ old('is_active', $onboarding->is_active ?? 1) ? 'checked' : '' }}
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
</div>

<script>
  (function(){
    const input = document.getElementById('onboarding_image');
    const preview = document.getElementById('onboarding_preview');
    if (input && preview) {
      input.addEventListener('change', function(){
        const f = this.files && this.files[0];
        if(!f) return;
        const url = URL.createObjectURL(f);
        preview.src = url;
      });
    }
  })();
</script>
