<div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm flex gap-3">
  <div class="flex-1">
    <div class="text-sm font-semibold">{{ $review['place_name'] }}</div>
    <div class="text-xs text-gray-500">{{ $review['created_at']->diffForHumans() }}</div>
    <div class="mt-2 text-sm text-gray-700">{{ \\Illuminate\Support\Str::limit($review['comment'], 140) }}</div>
  </div>
  <div class="flex items-center">
    <div class="text-xl">{{ $review['emoji'] }}</div>
  </div>
</div>
