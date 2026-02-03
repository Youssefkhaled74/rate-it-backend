@extends('admin.layouts.app')

@section('title','Edit Branch')

@section('content')
<div class="max-w-3xl">
  <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">

    <div class="px-6 md:px-8 py-6 border-b border-gray-100 bg-gradient-to-br from-white to-gray-50">
      <div class="flex items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">Edit Branch</h2>
          <p class="text-sm text-gray-500 mt-1">Update branch details and status.</p>
        </div>
        <a href="{{ route('admin.branches.index') }}"
           class="rounded-2xl bg-white border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
          Back
        </a>
      </div>
    </div>

    @if ($errors->any())
      <div class="mx-6 md:mx-8 mt-6 rounded-2xl bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3">
        <div class="font-semibold mb-1">Please fix the following:</div>
        <ul class="list-disc pl-5 space-y-1">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.branches.update', $branch) }}" class="px-6 md:px-8 py-6">
      @csrf
      @method('PUT')
      @include('admin.branches._form', ['branch' => $branch])

      <div class="mt-8 flex gap-3">
        <button class="flex-1 rounded-2xl bg-red-900 text-white py-3.5 text-sm font-semibold shadow-lg shadow-red-900/20 hover:bg-red-950 transition">
          Save Changes
        </button>
        <a href="{{ route('admin.branches.index') }}"
           class="flex-1 text-center rounded-2xl bg-white border border-gray-200 py-3.5 text-sm font-semibold text-gray-800 hover:bg-gray-50 transition">
          Cancel
        </a>
      </div>
    </form>

  </div>
</div>
@endsection
