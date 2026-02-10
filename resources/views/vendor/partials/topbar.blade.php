@php
  $vendor = $vendor ?? auth()->guard('vendor_web')->user();
  $brandName = $vendor?->brand?->name
    ?? $vendor?->branch?->place?->display_name
    ?? $vendor?->branch?->name
    ?? __('vendor.vendor');
  $brandLogo = $vendor?->brand?->logo
    ?? $vendor?->branch?->place?->brand?->logo
    ?? null;
  $brandLogoUrl = $brandLogo ? asset($brandLogo) : asset('assets/images/category-icon-placeholder.png');
  $roleLabel = $vendor?->role === 'VENDOR_ADMIN' ? __('vendor.dashboard') : __('vendor.verify_voucher');
  $currentLang = request()->get('lang', app()->getLocale());
@endphp

<div class="flex items-center justify-between bg-white/85 dark:bg-slate-900/85 backdrop-blur border border-gray-100 dark:border-slate-800 rounded-2xl px-6 py-4 shadow-sm">
  <div class="flex items-center gap-4">
    <div class="h-11 w-11 rounded-2xl bg-red-700 text-white flex items-center justify-center font-bold shadow-sm">
      {{ strtoupper(mb_substr($vendor?->name ?? 'V', 0, 1)) }}
    </div>
    <div>
      <div class="text-xs uppercase tracking-wide text-gray-400">{{ __('vendor.welcome') }}</div>
      <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
        {{ $vendor?->name ?? __('vendor.vendor') }}
      </div>
    </div>
  </div>

  <div class="flex items-center gap-2">
    <span class="text-[11px] uppercase tracking-wide px-3 py-1.5 rounded-full bg-red-50 text-red-700 border border-red-100 dark:bg-red-900/30 dark:text-red-200 dark:border-red-900/40">
      {{ $vendor?->role }}
    </span>
    <span class="text-[11px] px-3 py-1.5 rounded-full bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-300">
      {{ $roleLabel }}
    </span>

    <div class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-slate-800 p-1">
      <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
         class="text-[11px] px-2.5 py-1 rounded-full {{ $currentLang === 'en' ? 'bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-300' }}">
        EN
      </a>
      <a href="{{ request()->fullUrlWithQuery(['lang' => 'ar']) }}"
         class="text-[11px] px-2.5 py-1 rounded-full {{ $currentLang === 'ar' ? 'bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-300' }}">
        AR
      </a>
    </div>

    <button type="button" data-theme-toggle class="text-[11px] px-3 py-1.5 rounded-full bg-white dark:bg-slate-900 border border-gray-200 text-gray-700 dark:bg-slate-800 dark:border-slate-700 dark:text-gray-200">
      <span data-theme-label>Light</span>
    </button>
  </div>

  <div class="flex items-center gap-3">
    <div class="w-9 h-9 rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 overflow-hidden shadow-sm">
      <img src="{{ $brandLogoUrl }}" alt="Brand logo" class="w-full h-full object-contain">
    </div>
    <div class="text-right">
      <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $brandName }}</div>
    </div>
  </div>
</div>
