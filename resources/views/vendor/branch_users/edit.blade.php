@extends('vendor.layouts.app')

@section('title', __('vendor.edit_user'))

@section('content')
  <div class="mb-4">
    <a href="{{ route('vendor.staff.index') }}" class="text-xs font-semibold text-red-700">{{ __('vendor.back_to_staff') }}</a>
  </div>

  <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow max-w-xl">
    <div class="text-lg font-semibold mb-4">{{ __('vendor.edit_user') }}</div>

    <form method="POST" action="{{ route('vendor.staff.update', $staff->id) }}" class="space-y-4">
      @csrf
      <div>
        <label class="text-sm">{{ __('vendor.name') }}</label>
        <input name="name" value="{{ old('name', $staff->name) }}" class="w-full border rounded-lg px-3 py-2" />
        @error('name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="text-sm">{{ __('vendor.branch') }}</label>
        <select name="branch_id" class="w-full border rounded-lg px-3 py-2">
          @foreach($branches as $b)
            <option value="{{ $b->id }}" @selected(old('branch_id', $staff->branch_id) == $b->id)>{{ $b->name }}</option>
          @endforeach
        </select>
        @error('branch_id')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="text-sm">{{ __('vendor.status') }}</label>
        <select name="is_active" class="w-full border rounded-lg px-3 py-2">
          <option value="1" @selected(old('is_active', $staff->is_active) == 1)>{{ __('vendor.active') }}</option>
          <option value="0" @selected(old('is_active', $staff->is_active) == 0)>{{ __('vendor.inactive') }}</option>
        </select>
      </div>

      <button class="px-4 py-2 rounded-lg bg-red-700 text-white text-sm font-semibold">{{ __('vendor.save') }}</button>
    </form>
  </div>
@endsection


