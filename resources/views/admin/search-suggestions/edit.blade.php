@extends('admin.layouts.app')

@section('page_title', __('admin.edit_suggestion'))
@section('title', __('admin.edit_suggestion'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-semibold text-gray-900">{{ __('admin.edit_suggestion') }}</h2>
      <a href="{{ route('admin.search-suggestions.index') }}" class="text-sm text-gray-500">{{ __('admin.back') }}</a>
    </div>

    @if ($errors->any())
      <div class="mt-4 rounded-2xl bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3">
        <div class="font-semibold mb-1">{{ __('admin.fix_errors') }}</div>
        @foreach($errors->all() as $err)
          <div>{{ $err }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('admin.search-suggestions.update', $suggestion) }}" class="mt-6">
      @csrf
      @method('PATCH')
      @include('admin.search-suggestions._form')

      <div class="mt-6">
        <button type="submit"
                class="rounded-full bg-red-800 text-white px-6 py-2 text-sm font-semibold hover:bg-red-900 transition">
          {{ __('admin.save_changes') }}
        </button>
      </div>
    </form>
  </div>
@endsection
