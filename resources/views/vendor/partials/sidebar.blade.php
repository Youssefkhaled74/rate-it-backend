@php
  $vendor = $vendor ?? auth()->guard('vendor_web')->user();
  $role = $vendor?->role;

  $iconSvg = function (string $key) {
    $icons = [
      'home' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/></svg>',
      'star' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2l3 7 7 .6-5.3 4.6 1.6 7-6.3-3.8-6.3 3.8 1.6-7L2 9.6 9 9z"/></svg>',
      'gear' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 15.5A3.5 3.5 0 1 0 12 8.5a3.5 3.5 0 0 0 0 7z"/><path d="M19.4 15a7.9 7.9 0 0 0 .1-2l2-1.2-2-3.4-2.3.6a8 8 0 0 0-1.7-1L15 3h-6l-.5 3a8 8 0 0 0-1.7 1L4.5 6.4l-2 3.4 2 1.2a8 8 0 0 0 0 2l-2 1.2 2 3.4 2.3-.6a8 8 0 0 0 1.7 1L9 21h6l.5-3a8 8 0 0 0 1.7-1l2.3.6 2-3.4-2.1-1.2z"/></svg>',
      'users' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
      'check' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 12l4 4 12-12"/></svg>',
      'receipt' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2h12v20l-3-2-3 2-3-2-3 2z"/><path d="M9 6h6"/><path d="M9 10h6"/><path d="M9 14h6"/></svg>',
      'logout' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>',
    ];

    return $icons[$key] ?? '';
  };
@endphp

<aside class="vendor-sidebar p-5">
  <div class="vendor-sidebar-fixed">
    <div class="vendor-sidebar-inner">
      <div class="flex items-center vendor-logo-row mb-6">
        <div class="w-12 h-12 rounded-2xl bg-red-800 border border-white/10 grid place-items-center overflow-hidden shadow-lg shadow-black/10">
          <img src="{{ asset('assets/images/Vector.png') }}" alt="Rateit" class="w-9 h-9 object-contain">
        </div>
        <div class="text-white">
          <div class="text-base font-semibold">Rateit</div>
          <div class="text-xs opacity-80">{{ __('vendor.panel') }}</div>
        </div>
      </div>

      <nav class="space-y-1 max-h-[70vh] overflow-y-auto vendor-nav-scroll">
        @if($role === 'VENDOR_ADMIN')
          <a href="{{ route('vendor.dashboard') }}" class="flex items-center gap-3 vendor-nav-item {{ request()->routeIs('vendor.dashboard') ? 'is-active' : '' }}">
            <span class="vendor-nav-icon text-white/90">{!! $iconSvg('home') !!}</span>
            <span class="vendor-nav-text">{{ __('vendor.dashboard') }}</span>
          </a>
          <a href="{{ route('vendor.reviews.index') }}" class="flex items-center gap-3 vendor-nav-item {{ request()->routeIs('vendor.reviews.*') ? 'is-active' : '' }}">
            <span class="vendor-nav-icon text-white/90">{!! $iconSvg('star') !!}</span>
            <span class="vendor-nav-text">{{ __('vendor.reviews') }}</span>
          </a>
          <a href="{{ route('vendor.branches.settings') }}" class="flex items-center gap-3 vendor-nav-item {{ request()->routeIs('vendor.branches.*') ? 'is-active' : '' }}">
            <span class="vendor-nav-icon text-white/90">{!! $iconSvg('gear') !!}</span>
            <span class="vendor-nav-text">{{ __('vendor.branch_settings') }}</span>
          </a>
          <a href="{{ route('vendor.staff.index') }}" class="flex items-center gap-3 vendor-nav-item {{ request()->routeIs('vendor.staff.*') ? 'is-active' : '' }}">
            <span class="vendor-nav-icon text-white/90">{!! $iconSvg('users') !!}</span>
            <span class="vendor-nav-text">{{ __('vendor.branch_users') }}</span>
          </a>
        @endif

        <a href="{{ route('vendor.vouchers.verify') }}" class="flex items-center gap-3 vendor-nav-item {{ request()->routeIs('vendor.vouchers.verify') ? 'is-active' : '' }}">
          <span class="vendor-nav-icon text-white/90">{!! $iconSvg('check') !!}</span>
          <span class="vendor-nav-text">{{ __('vendor.verify_voucher') }}</span>
        </a>
        <a href="{{ route('vendor.vouchers.history') }}" class="flex items-center gap-3 vendor-nav-item {{ request()->routeIs('vendor.vouchers.history') ? 'is-active' : '' }}">
          <span class="vendor-nav-icon text-white/90">{!! $iconSvg('receipt') !!}</span>
          <span class="vendor-nav-text">{{ __('vendor.voucher_history') }}</span>
        </a>
      </nav>

      <form method="POST" action="{{ route('vendor.logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="flex items-center gap-3 text-white/90 hover:text-white vendor-logout w-full">
          <span class="vendor-nav-icon">{!! $iconSvg('logout') !!}</span>
          <span>{{ __('vendor.logout') }}</span>
        </button>
      </form>
    </div>
  </div>
</aside>


