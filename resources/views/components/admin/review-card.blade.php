@props([
  'name' => 'Ahmed Mohamed',
  'meta' => '6 hours ago • Cairo, Egypt',
  'text' => 'Sample review text...',
  'status' => 'urgent', // urgent|high|normal
])

@php
  $bar = match($status){
    'urgent' => 'bg-red-700',
    'high'   => 'bg-yellow-500',
    default  => 'bg-green-500',
  };
@endphp

<div class="bg-white rounded-3xl shadow-soft overflow-hidden">
  <div class="flex">
    <div class="w-2 {{ $bar }}"></div>
    <div class="p-5 flex-1">
      <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-gray-200"></div>
          <div>
            <div class="text-sm font-semibold">{{ $name }}</div>
            <div class="text-xs text-gray-500">{{ $meta }}</div>
          </div>
        </div>
        <div class="text-xl">😡</div>
      </div>

      <p class="text-sm text-gray-700 mt-3 leading-relaxed">
        "{{ $text }}"
      </p>

      <div class="mt-4 flex items-center justify-between">
        <div class="text-xs text-gray-500">
          👍 8 helpful • 📷 1 photo • ⚠️ Low rating • Negative keywords
        </div>
      </div>

      <div class="mt-4 flex items-center gap-3">
        <button class="flex-1 rounded-full bg-red-800 text-white py-3 text-sm font-semibold hover:bg-red-900 transition">
          Quick Reply
        </button>
        <button class="w-12 h-12 rounded-full border border-gray-200 bg-white grid place-items-center">
          ➜
        </button>
      </div>
    </div>
  </div>
</div>
