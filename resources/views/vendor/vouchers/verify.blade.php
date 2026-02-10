@extends('vendor.layouts.app')

@section('title', __('vendor.verify_voucher'))

@section('content')
  <div class="text-lg font-semibold mb-4">{{ __('vendor.verify_voucher') }}</div>

  @if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm">{{ session('success') }}</div>
  @endif

  <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow max-w-2xl">
    <form method="POST" action="{{ route('vendor.vouchers.check') }}" class="flex flex-col md:flex-row gap-3">
      @csrf
      <input type="text" name="code_or_link" value="{{ old('code_or_link') }}" class="flex-1 border rounded-lg px-3 py-2" placeholder="{{ __('vendor.voucher_code_or_link') }}">
      <button class="px-4 py-2 rounded-lg bg-red-700 text-white text-sm font-semibold">{{ __('vendor.check') }}</button>
    </form>
    @error('code_or_link')<div class="text-xs text-red-600 mt-2">{{ $message }}</div>@enderror

    @if($voucher)
      <div class="mt-6 border rounded-xl p-4 bg-gray-50 dark:bg-slate-800/50">
        <div class="flex items-center justify-between">
          <div class="text-sm font-semibold">{{ __('vendor.voucher') }}: {{ $voucher->code }}</div>
          <div class="text-xs px-2 py-1 rounded-full
            {{ $status === 'VALID' ? 'bg-green-100 text-green-700' : ($status === 'USED' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-700') }}">
            {{ $status }}
          </div>
        </div>

        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div><span class="text-gray-500 dark:text-gray-400">{{ __('vendor.brand') }}:</span> {{ $voucher->brand?->name ?? '-' }}</div>
          <div><span class="text-gray-500 dark:text-gray-400">{{ __('vendor.expires_at') }}:</span> {{ $voucher->expires_at?->format('Y-m-d H:i') ?? '-' }}</div>
          <div><span class="text-gray-500 dark:text-gray-400">{{ __('vendor.used_at') }}:</span> {{ $voucher->used_at?->format('Y-m-d H:i') ?? '-' }}</div>
          <div><span class="text-gray-500 dark:text-gray-400">{{ __('vendor.used_branch') }}:</span> {{ $voucher->usedBranch?->name ?? '-' }}</div>
        </div>

        @if($status === 'VALID')
          <form method="POST" action="{{ route('vendor.vouchers.redeem') }}" class="mt-4 flex flex-col md:flex-row gap-3">
            @csrf
            <input type="hidden" name="code_or_link" value="{{ $voucher->code }}">
            @if(($vendor->role ?? '') === 'VENDOR_ADMIN')
              <select name="branch_id" class="border rounded-lg px-3 py-2">
                <option value="">{{ __('vendor.select_branch') }}</option>
                @foreach($branches as $b)
                  <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
              </select>
            @endif
            <button class="px-4 py-2 rounded-lg bg-green-700 text-white text-sm font-semibold">{{ __('vendor.confirm_redeem') }}</button>
          </form>
        @endif
      </div>
    @endif
  </div>
@endsection


