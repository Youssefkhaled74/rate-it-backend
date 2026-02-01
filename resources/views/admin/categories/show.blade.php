@extends('admin.layouts.app')

@section('title', $category->name_en ?? 'Category')

@section('content')
<div class="max-w-4xl">
  <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">

    <div class="px-6 md:px-8 py-6 border-b border-gray-100 bg-gradient-to-br from-white to-gray-50">
      <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-50 border border-gray-100 grid place-items-center">
            <img src="{{ $category->icon ? asset($category->icon) : asset('assets/images/category-icon-placeholder.png') }}" class="w-14 h-14 object-cover rounded-full">
          </div>
          <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ $category->name_en }}</h2>
            <div class="text-sm text-gray-500">{{ $category->name_ar ?: '—' }}</div>
          </div>
        </div>

        <div class="flex gap-3">
          <a href="{{ route('admin.categories.edit', $category) }}" class="rounded-2xl bg-white border border-gray-200 px-4 py-2 text-sm font-semibold hover:bg-gray-50">Edit</a>
          <a href="{{ route('admin.categories.index') }}" class="rounded-2xl bg-white border border-gray-200 px-4 py-2 text-sm font-semibold hover:bg-gray-50">Back</a>
        </div>
      </div>
    </div>

    <div class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
          <div class="mb-4">
            <div class="text-sm text-gray-600">Cover Image</div>
            <div class="mt-2 rounded-2xl overflow-hidden border border-gray-100 bg-gray-50">
              <img src="{{ $category->logo ? asset($category->logo) : asset('assets/images/category-placeholder.png') }}" class="w-full h-44 object-cover">
            </div>
          </div>

          <div>
            <h3 class="text-lg font-semibold mb-3">Subcategories ({{ $category->subcategories->count() }})</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              @forelse($category->subcategories as $sub)
                <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                  <div class="w-12 h-12 rounded-full bg-gray-50 overflow-hidden border border-gray-100 grid place-items-center flex-shrink-0">
                    @if($sub->image)
                      <img src="{{ asset($sub->image) }}" class="w-12 h-12 object-cover rounded-full" alt="{{ $sub->name_en }}">
                    @else
                      <div class="w-10 h-10 rounded-full bg-gray-200"></div>
                    @endif
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="font-medium text-gray-900 truncate">{{ $sub->name_en }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ $sub->name_ar ?: '—' }}</div>
                  </div>
                  <div class="text-sm text-gray-500">{{ $sub->is_active ? 'Active' : 'Inactive' }}</div>
                </div>
              @empty
                <div class="text-sm text-gray-500">No subcategories yet.</div>
              @endforelse
            </div>
          </div>
        </div>

        <aside class="p-4 border border-gray-100 rounded-2xl bg-gray-50">
          <div class="text-sm text-gray-600">Details</div>
          <div class="mt-3 space-y-2 text-sm text-gray-700">
            <div><strong>Active:</strong> {{ $category->is_active ? 'Yes' : 'No' }}</div>
            <div><strong>Sort order:</strong> {{ $category->sort_order ?? '—' }}</div>
            <div><strong>Created:</strong> {{ $category->created_at?->toDayDateTimeString() }}</div>
          </div>
        </aside>
      </div>
    </div>

  </div>
</div>
@endsection
