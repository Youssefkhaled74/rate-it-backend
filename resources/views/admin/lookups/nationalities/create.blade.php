@extends('admin.layouts.app')

@section('title', __('admin.add_nationality'))

@section('content')
<div class="max-w-3xl">
  <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">

    <div class="px-6 md:px-8 py-6 border-b border-gray-100 bg-gradient-to-br from-white to-gray-50">
      <div class="flex items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">{{ __('admin.add_nationality') }}</h2>
          <p class="text-sm text-gray-500 mt-1">{{ __('admin.manage_nationalities') }}</p>
        </div>
        <a href="{{ route('admin.lookups.nationalities.index', ['lang' => request('lang')]) }}"
           class="rounded-2xl bg-white border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
          {{ __('admin.back') }}
        </a>
      </div>
    </div>

    @if ($errors->any())
      <div class="mx-6 md:mx-8 mt-6 rounded-2xl bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3">
        <div class="font-semibold mb-1">{{ __('admin.fix_errors') }}</div>
        <ul class="list-disc pl-5 space-y-1">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.lookups.nationalities.store') }}" class="px-6 md:px-8 py-6">
      @csrf
      @include('admin.lookups.nationalities._form', ['nationality' => null])

      <div class="mt-8">
        <button
          class="w-full rounded-2xl bg-red-900 text-white py-3.5 text-sm font-semibold shadow-lg shadow-red-900/20 hover:bg-red-950 transition">
          {{ __('admin.save') }}
        </button>
      </div>
    </form>

  </div>
</div>
@endsection
