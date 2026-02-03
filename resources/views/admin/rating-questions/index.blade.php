@extends('admin.layouts.app')

@section('title','Rating Questions')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <h2 class="text-2xl font-semibold text-gray-900">Questions</h2>

    <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
      <div class="w-full max-w-md">
        <form method="get" class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="11" cy="11" r="7"></circle>
              <path d="M21 21l-4.3-4.3"></path>
            </svg>
          </span>
          <input
            name="q"
            value="{{ request('q') }}"
            placeholder="Search"
            class="w-full rounded-2xl border border-gray-200 bg-white/80 pl-11 pr-4 py-3 text-sm outline-none
                   focus:border-red-300 focus:ring-4 focus:ring-red-100 transition"
          >
        </form>
      </div>

      <div class="flex items-center gap-3">
        <form method="get" class="flex items-center gap-3">
          <input type="hidden" name="q" value="{{ request('q') }}">
          <select name="subcategory_id" onchange="this.form.submit()"
                  class="h-11 rounded-2xl border border-gray-200 bg-white px-4 text-sm text-gray-700">
            <option value="">All Subcategories</option>
            @foreach($subcategories as $sub)
              <option value="{{ $sub->id }}" {{ (string) $subcategoryId === (string) $sub->id ? 'selected' : '' }}>
                {{ $sub->name_en }} {{ $sub->category?->name_en ? '— ' . $sub->category->name_en : '' }}
              </option>
            @endforeach
          </select>

          <select name="type" onchange="this.form.submit()"
                  class="h-11 rounded-2xl border border-gray-200 bg-white px-4 text-sm text-gray-700">
            <option value="">All Types</option>
            <option value="RATING" {{ request('type') === 'RATING' ? 'selected' : '' }}>Rating</option>
            <option value="YES_NO" {{ request('type') === 'YES_NO' ? 'selected' : '' }}>Yes / No</option>
            <option value="MULTIPLE_CHOICE" {{ request('type') === 'MULTIPLE_CHOICE' ? 'selected' : '' }}>Multiple Choice</option>
          </select>
        </form>

        <a href="{{ route('admin.rating-questions.create') }}"
           class="h-11 inline-flex items-center rounded-2xl bg-red-900 px-4 text-sm font-semibold text-white shadow-lg shadow-red-900/20 hover:bg-red-950 transition">
          Add Question
        </a>
      </div>
    </div>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-[22px] bg-white border border-gray-100 shadow-soft p-5">
      <div class="text-sm text-gray-600">Total Rating Questions</div>
      <div class="mt-2 text-3xl font-semibold text-red-900">{{ $totalRating }}</div>
    </div>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">
    <div class="grid grid-cols-12 gap-4 px-6 py-4 text-xs font-semibold text-gray-500">
      <div class="col-span-5">Question Text</div>
      <div class="col-span-3">Subcategories</div>
      <div class="col-span-2">Status</div>
      <div class="col-span-2 text-right">Actions</div>
    </div>

    <div class="divide-y divide-gray-100">
      @forelse($questions as $q)
        <div class="grid grid-cols-12 gap-4 px-6 py-4 items-center">
          <div class="col-span-5">
            <div class="text-sm text-gray-900">
              {{ $q->question_en ?: $q->question_text }}
            </div>
            <div class="text-[11px] text-gray-500 mt-1">{{ $q->type }}</div>
          </div>
          <div class="col-span-3">
            <div class="text-sm text-gray-900">{{ $q->subcategory?->name_en ?: '-' }}</div>
            <div class="text-[11px] text-gray-500">{{ $q->subcategory?->category?->name_en ?: '' }}</div>
          </div>
          <div class="col-span-2">
            <form method="POST" action="{{ route('admin.rating-questions.toggle', $q) }}">
              @csrf
              @method('PATCH')
              <button type="submit"
                class="w-10 h-6 rounded-full {{ $q->is_active ? 'bg-red-900' : 'bg-gray-200' }} relative transition">
                <span class="absolute top-0.5 {{ $q->is_active ? 'left-5' : 'left-0.5' }} w-5 h-5 rounded-full bg-white transition"></span>
              </button>
            </form>
          </div>
          <div class="col-span-2 flex items-center justify-end gap-2">
            <a href="{{ route('admin.rating-questions.edit', $q) }}"
               class="w-9 h-9 rounded-full bg-yellow-50 border border-yellow-100 grid place-items-center text-yellow-700 hover:bg-yellow-100">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21 7.5a2.5 2.5 0 0 0-2.5-2.5H7a2.5 2.5 0 0 0-2.5 2.5v9A2.5 2.5 0 0 0 7 19h10.5a2.5 2.5 0 0 0 2.5-2.5v-9Z"/>
              </svg>
            </a>
            <form method="POST" action="{{ route('admin.rating-questions.destroy', $q) }}">
              @csrf
              @method('DELETE')
              <button type="button" data-confirm="delete-question-{{ $q->id }}" data-confirm-text="Delete" data-title="Delete question?" data-message="Are you sure you want to delete this question?"
                      class="w-9 h-9 rounded-full bg-red-50 border border-red-100 grid place-items-center text-red-700 hover:bg-red-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M9 3h6l1 2h4v2H4V5h4l1-2Zm1 6h2v8h-2V9Zm4 0h2v8h-2V9ZM7 9h2v8H7V9Z"/>
                </svg>
              </button>
              <input type="hidden" name="_confirm_target" value="delete-question-{{ $q->id }}" />
            </form>
          </div>
        </div>
      @empty
        <div class="px-6 py-8 text-sm text-gray-500">No questions found.</div>
      @endforelse
    </div>
  </div>

  <div>
    {{ $questions->links() }}
  </div>
</div>
@endsection
