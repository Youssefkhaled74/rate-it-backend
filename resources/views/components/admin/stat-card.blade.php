@props([
  'title' => '',
  'value' => '',
  'sub' => '',
  'accent' => 'text-green-600', // or text-red-600
])

<div class="bg-white rounded-2xl p-6 shadow-soft flex-1">
  <div class="flex items-center gap-4">
    <div class="w-12 h-12 rounded-full bg-yellow-100 grid place-items-center">⭐</div>
    <div>
      <div class="text-2xl font-semibold">{{ $value }}</div>
      <div class="text-sm text-gray-500">{{ $title }}</div>
      @if($sub)
        <div class="text-sm {{ $accent }} mt-1">{{ $sub }}</div>
      @endif
    </div>
  </div>
</div>
