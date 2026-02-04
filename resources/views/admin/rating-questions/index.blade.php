@extends('admin.layouts.app')

@section('title', __('admin.rating_questions'))

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.questions') }}</h2>

    <div class="w-full lg:w-auto">
      <div class="bg-white border border-gray-100 rounded-3xl shadow-soft p-3">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
          <form method="get" class="flex flex-1 flex-col gap-3 lg:flex-row lg:items-center">
            <div class="relative w-full lg:w-[360px]">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 rtl-search-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <circle cx="11" cy="11" r="7"></circle>
                  <path d="M21 21l-4.3-4.3"></path>
                </svg>
              </span>
              <input
                name="q"
                value="{{ request('q') }}"
                placeholder="{{ __('admin.search') }}"
                class="w-full rounded-2xl border border-gray-200 bg-white pl-11 pr-4 py-3 text-sm outline-none rtl-search-input
                       focus:border-red-300 focus:ring-4 focus:ring-red-100 transition"
              >
            </div>

                        <x-admin.select
              name="subcategory_id"
              :placeholder="__('admin.all_subcategories')"
              wrapperClass="w-full lg:w-[260px]"
              selectClass="h-11 text-gray-700"
              onchange="this.form.submit()"
            >
              @foreach($subcategories as $sub)
                <option value="{{ $sub->id }}" {{ (string) $subcategoryId === (string) $sub->id ? 'selected' : '' }}>
                  {{ $sub->name_en }} {{ $sub->category?->name_en ? '— ' . $sub->category->name_en : '' }}
                </option>
              @endforeach
            </x-admin.select>

            <x-admin.select
              name="type"
              :placeholder="__('admin.all_types')"
              wrapperClass="w-full lg:w-[180px]"
              selectClass="h-11 text-gray-700"
              onchange="this.form.submit()"
            >
              <option value="RATING" {{ request('type') === 'RATING' ? 'selected' : '' }}>{{ __('admin.rating') }}</option>
              <option value="YES_NO" {{ request('type') === 'YES_NO' ? 'selected' : '' }}>{{ __('admin.yes_no') }}</option>
              <option value="MULTIPLE_CHOICE" {{ request('type') === 'MULTIPLE_CHOICE' ? 'selected' : '' }}>{{ __('admin.multiple_choice') }}</option>
            </x-admin.select>
          </form>

          <a href="{{ route('admin.rating-questions.create') }}"
             class="h-11 inline-flex items-center justify-center rounded-2xl bg-red-900 px-5 text-sm font-semibold text-white shadow-lg shadow-red-900/20 hover:bg-red-950 transition">
            {{ __('admin.add_question') }}
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-[22px] bg-white border border-gray-100 shadow-soft p-5">
      <div class="text-sm text-gray-600">{{ __('admin.total_rating_questions') }}</div>
      <div class="mt-2 text-3xl font-semibold text-red-900">{{ $totalRating }}</div>
    </div>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">
    <div class="grid grid-cols-12 gap-4 px-6 py-4 text-xs font-semibold text-gray-500">
      <div class="col-span-5">{{ __('admin.question_text') }}</div>
      <div class="col-span-3">{{ __('admin.subcategories') }}</div>
      <div class="col-span-2">{{ __('admin.status') }}</div>
      <div class="col-span-2 text-right">{{ __('admin.actions') }}</div>
    </div>

    <div class="divide-y divide-gray-100">
      @forelse($questions as $q)
        <div class="grid grid-cols-12 gap-4 px-6 py-4 items-center">
          <div class="col-span-5">
            <div class="text-sm text-gray-900">
              {{ $q->question_en ?: $q->question_text }}
            </div>
            <div class="text-[11px] text-gray-500 mt-1">{{ __($q->type === 'RATING' ? 'admin.rating' : ($q->type === 'YES_NO' ? 'admin.yes_no' : 'admin.multiple_choice')) }}</div>
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
              <button type="button" data-confirm="delete-question-{{ $q->id }}" data-confirm-text="{{ __('admin.delete') }}" data-title="{{ __('admin.delete') }}" data-message="{{ __('admin.confirm_message') }}"
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
        <div class="px-6 py-8 text-sm text-gray-500">{{ __('admin.no_questions_found') }}</div>
      @endforelse
    </div>
  </div>

  <div>
    {{ $questions->links() }}
  </div>
</div>
@endsection

