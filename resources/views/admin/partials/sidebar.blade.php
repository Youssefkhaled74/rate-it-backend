@php
  // Sidebar items (عدل الروتس براحتك)
  $currentLang = request()->get('lang', app()->getLocale());
  $items = [
    ['label' => __('admin.dashboard'), 'route' => 'admin.dashboard', 'icon' => 'grid'],
    ['label' => __('admin.categories'), 'route' => 'admin.categories.index', 'icon' => 'layers'],
    ['label' => __('admin.brands'), 'route' => 'admin.brands.index', 'icon' => 'tag'],
    ['label' => __('admin.places'), 'route' => 'admin.places.index', 'icon' => 'home'],
    ['label' => __('admin.branches'), 'route' => 'admin.branches.index', 'icon' => 'branch'],
    ['label' => __('admin.reviews'), 'route' => 'admin.reviews.index', 'icon' => 'star'],
    ['label' => __('admin.users'), 'route' => 'admin.users.index', 'icon' => 'users'],
    ['label' => __('admin.lookups'), 'route' => 'admin.lookups.index', 'icon' => 'search'],
    ['label' => __('admin.rewards_system'), 'route' => 'admin.rewards.index', 'icon' => 'trophy'],
    ['label' => __('admin.rating_questions'), 'route' => 'admin.rating-questions.index', 'icon' => 'checklist'],
    ['label' => __('admin.subscription_plans'), 'route' => 'admin.subscription-plans.index', 'icon' => 'trophy'],
    ['label' => __('admin.banners_onboarding'), 'route' => 'admin.banners.index', 'icon' => 'flag'],
    ['label' => __('admin.notifications'), 'route' => 'admin.notifications.send', 'icon' => 'bell'],
    ['label' => __('admin.subscription_settings'), 'route' => 'admin.settings.index', 'icon' => 'gear'],
  ];

  // Add Admins menu if permitted
  $currentAdmin = auth()->guard('admin_web')->user();
  $canViewAdmins = false;
  if ($currentAdmin) {
    if (strtoupper($currentAdmin->role ?? '') === 'SUPER_ADMIN') $canViewAdmins = true;
    elseif (method_exists($currentAdmin, 'permissions') && $currentAdmin->permissions()->where('name', 'admins.view')->exists()) $canViewAdmins = true;
  }
  if ($canViewAdmins) {
    $items[] = ['label' => __('admin.admins'), 'route' => 'admin.admins.index', 'icon' => 'users'];
  }

  // helper لمعرفة الـ active
  $isActive = function ($routeName) {
    return request()->routeIs($routeName) ? true : false;
  };

  // SVG icons (simple, inline)
  $iconSvg = function ($name) {
    return match ($name) {
      'grid' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h7v7H4z"/><path d="M13 4h7v7h-7z"/><path d="M4 13h7v7H4z"/><path d="M13 13h7v7h-7z"/></svg>',
      'layers' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 3 8l9 5 9-5-9-5z"/><path d="M3 12l9 5 9-5"/><path d="M3 16l9 5 9-5"/></svg>',
      'tag' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 12l-8 8-9-9V4h7l10 8z"/><circle cx="7.5" cy="7.5" r="1"/></svg>',
      'users' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
      'star' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2l3 7 7 .6-5.3 4.6 1.6 7-6.3-3.8-6.3 3.8 1.6-7L2 9.6 9 9z"/></svg>',
      'gear' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 15.5A3.5 3.5 0 1 0 12 8.5a3.5 3.5 0 0 0 0 7z"/><path d="M19.4 15a7.9 7.9 0 0 0 .1-2l2-1.2-2-3.4-2.3.6a8 8 0 0 0-1.7-1L15 3h-6l-.5 3a8 8 0 0 0-1.7 1L4.5 6.4l-2 3.4 2 1.2a8 8 0 0 0 0 2l-2 1.2 2 3.4 2.3-.6a8 8 0 0 0 1.7 1L9 21h6l.5-3a8 8 0 0 0 1.7-1l2.3.6 2-3.4-2.1-1.2z"/></svg>',
      'pin' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-7-4.6-7-11a7 7 0 0 1 14 0c0 6.4-7 11-7 11z"/><circle cx="12" cy="10" r="3"/></svg>',
      'home' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/></svg>',
      'branch' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="12" r="3"/><path d="M8.7 7.6l6.6 3.5M8.7 16.4l6.6-3.5"/></svg>',
      'search' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>',
      'trophy' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 4h8v3a4 4 0 1 1-8 0z"/><path d="M6 4H4a2 2 0 0 0 2 2"/><path d="M18 4h2a2 2 0 0 1-2 2"/><path d="M12 12v4"/><path d="M8 20h8"/><path d="M10 16h4"/></svg>',
      'checklist' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 6h11"/><path d="M9 12h11"/><path d="M9 18h11"/><path d="M4 6l1 1 2-2"/><path d="M4 12l1 1 2-2"/><path d="M4 18l1 1 2-2"/></svg>',
      'flag' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4v16"/><path d="M4 5h12l-1.5 3L16 11H4z"/></svg>',
      'bell' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>',
      default => '<span class="w-5 h-5 inline-block"></span>',
    };
  };
@endphp

<aside class="admin-sidebar-spacer w-72 p-4">
  <div class="admin-sidebar-fixed admin-sidebar admin-sidebar-inner h-[calc(100vh-2rem)] fixed top-5 left-5 bottom-5 text-white flex flex-col">

    {{-- Logo --}}
    <div class="flex items-center admin-logo-row mb-8">
      <div class="w-12 h-12 rounded-2xl bg-red-800 border border-white/10 grid place-items-center overflow-hidden shadow-lg shadow-black/10">
        <img src="{{ asset('assets/images/Vector.png') }}" alt="Rateit" class="w-9 h-9 object-contain">
      </div>
      <div>
        <div class="text-lg font-semibold leading-tight">Rateit</div>
        <div class="text-xs text-white/70">{{ __('admin.admin_panel') }}</div>
      </div>
    </div>

    {{-- Nav --}}
    <div class="admin-nav-scroll flex-1 overflow-y-auto pr-1">
      <nav class="space-y-1">
        @foreach($items as $it)
          @php $active = $isActive($it['route']); @endphp

          <a href="{{ Route::has($it['route']) ? route($it['route'], ['lang' => $currentLang]) : '#' }}"
             class="admin-nav-item group flex items-center gap-3 px-4 py-3 transition
                    {{ $active ? 'is-active' : '' }}">
            <span class="admin-nav-icon text-white/90">{!! $iconSvg($it['icon']) !!}</span>
            <span class="admin-nav-text text-sm font-medium">{{ $it['label'] }}</span>
          </a>
        @endforeach
      </nav>
    </div>

    {{-- Logout bottom --}}
    <div class="mt-auto pt-6">
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit"
          class="admin-logout w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 transition text-left">
          <span class="text-white/90">
            {{-- logout icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <path d="M16 17l5-5-5-5"/>
              <path d="M21 12H9"/>
            </svg>
          </span>
          <span class="text-sm font-medium">{{ __('admin.logout') }}</span>
        </button>
      </form>
    </div>

  </div>
</aside>
