@extends('admin.layouts.app')

@section('title', __('admin.edit_brand'))

@section('content')
<div class="max-w-6xl space-y-6">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">

    <div class="px-6 md:px-8 py-6 border-b border-gray-100 bg-gradient-to-br from-white to-gray-50">
      <div class="flex items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">{{ __('admin.edit_brand') }}</h2>
          <p class="text-sm text-gray-500 mt-1">{{ __('admin.edit_brand_hint') }}</p>
        </div>
        <a href="{{ route('admin.brands.index') }}"
           class="rounded-2xl bg-white border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
          {{ __('admin.back') }}
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

    <form method="POST" action="{{ route('admin.brands.update', $brand) }}" enctype="multipart/form-data" class="px-6 md:px-8 py-6">
      @csrf
      @method('PUT')
      @include('admin.brands._form', ['brand' => $brand])

      <div class="mt-8 flex flex-col sm:flex-row gap-3">
        <button class="sm:flex-1 rounded-2xl bg-red-900 text-white py-3.5 text-sm font-semibold shadow-lg shadow-red-900/20 hover:bg-red-950 transition">
          {{ __('admin.save_changes') }}
        </button>
        <a href="{{ route('admin.brands.index') }}"
           class="sm:flex-1 text-center rounded-2xl bg-white border border-gray-200 py-3.5 text-sm font-semibold text-gray-800 hover:bg-gray-50 transition">
          {{ __('admin.cancel') }}
        </a>
      </div>
    </form>

    </div>

    <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">
      <div class="px-6 md:px-8 py-6 border-b border-gray-100 bg-gradient-to-br from-white to-gray-50">
        <div class="flex items-center justify-between gap-4">
          <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ __('admin.brand_admin') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('admin.brand_admin_hint') }}</p>
          </div>
          @if(!empty($vendorAdmin))
            <span class="text-xs font-semibold rounded-full px-3 py-1 bg-green-50 text-green-700 border border-green-100">{{ __('admin.assigned') }}</span>
          @else
            <span class="text-xs font-semibold rounded-full px-3 py-1 bg-yellow-50 text-yellow-700 border border-yellow-100">{{ __('admin.not_assigned') }}</span>
          @endif
        </div>
      </div>

      <form method="POST" action="{{ route('admin.brands.vendor-admin.save', $brand) }}" class="px-6 md:px-8 py-6">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <div class="rounded-2xl border border-rose-100 bg-rose-50/40 px-4 py-3 text-xs text-rose-700">
              {{ $vendorAdmin ? __('admin.brand_admin_keep_password') : __('admin.brand_admin_set_password') }}
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('admin.name') }}</label>
            <input type="text" name="name" value="{{ old('name', $vendorAdmin->name ?? '') }}"
                   class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('admin.phone') }}</label>
            <input type="text" name="phone" value="{{ old('phone', $vendorAdmin->phone ?? '') }}"
                   class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('admin.email') }}</label>
            <input type="email" name="email" value="{{ old('email', $vendorAdmin->email ?? '') }}"
                   class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('admin.password') }}</label>
            <input type="password" name="password"
                   placeholder="{{ $vendorAdmin ? __('admin.leave_blank_to_keep') : '' }}"
                   class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700">{{ __('admin.confirm_password') }}</label>
            <input type="password" name="password_confirmation"
                   class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
          </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
          <button class="sm:flex-1 rounded-2xl bg-red-900 text-white px-6 py-3 text-sm font-semibold shadow-lg shadow-red-900/20 hover:bg-red-950 transition">
            {{ $vendorAdmin ? __('admin.update_brand_admin') : __('admin.create_brand_admin') }}
          </button>
          <a href="{{ route('admin.brands.index') }}"
             class="sm:flex-1 text-center rounded-2xl bg-white border border-gray-200 px-6 py-3 text-sm font-semibold text-gray-800 hover:bg-gray-50 transition">
            {{ __('admin.back_to_brands') }}
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
