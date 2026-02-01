@php
  // Sidebar items (عدل الروتس براحتك)
  $items = [
    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'grid'],
    ['label' => 'Categories', 'route' => 'admin.categories.index', 'icon' => 'layers'],
    ['label' => 'Brands', 'route' => 'admin.brands.index', 'icon' => 'tag'],
    ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'users'],
    ['label' => 'Rewards System', 'route' => 'admin.rewards.index', 'icon' => 'star'],
    ['label' => 'Setting', 'route' => 'admin.settings.index', 'icon' => 'gear'],
  ];

  // Add Admins menu if permitted
  $currentAdmin = auth()->guard('admin_web')->user();
  $canViewAdmins = false;
  if ($currentAdmin) {
    if (strtoupper($currentAdmin->role ?? '') === 'SUPER_ADMIN') $canViewAdmins = true;
    elseif (method_exists($currentAdmin, 'permissions') && $currentAdmin->permissions()->where('name', 'admins.view')->exists()) $canViewAdmins = true;
  }
  if ($canViewAdmins) {
    $items[] = ['label' => 'Admins', 'route' => 'admin.admins.index', 'icon' => 'users'];
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
      default => '<span class="w-5 h-5 inline-block"></span>',
    };
  };
@endphp

<aside class="w-72 p-4">
  <div class="h-[calc(100vh-2rem)] fixed w-60 top-5 left-5 bottom-5 rounded-[42px] bg-red-900 text-white shadow-[0_30px_90px_rgba(0,0,0,.18)] px-6 py-7 flex flex-col">

    {{-- Logo --}}
    <div class="flex items-center gap-3 mb-8">
      <div class="w-12 h-12 rounded-2xl  bg-red-800 border border-white/10 grid place-items-center overflow-hidden shadow-lg shadow-black/10">
        <img src="{{ asset('assets/images/Vector.png') }}" alt="Rateit" class="w-9 h-9 object-contain">
      </div>
      <div>
        <div class="text-lg font-semibold leading-tight">Rateit</div>
        <div class="text-xs text-white/70">Admin Panel</div>
      </div>
    </div>

    {{-- Nav --}}
    <nav class="space-y-1">
      @foreach($items as $it)
        @php $active = $isActive($it['route']); @endphp

        <a href="{{ Route::has($it['route']) ? route($it['route']) : '#' }}"
           class="group flex items-center gap-3 px-4 py-3 rounded-2xl transition
                  {{ $active ? 'bg-white/18' : 'hover:bg-white/10' }}">
          <span class="text-white/90">{!! $iconSvg($it['icon']) !!}</span>
          <span class="text-sm font-medium">{{ $it['label'] }}</span>
        </a>
      @endforeach
    </nav>

    {{-- Logout bottom --}}
    <div class="mt-auto pt-6">
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit"
          class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 transition text-left">
          <span class="text-white/90">
            {{-- logout icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <path d="M16 17l5-5-5-5"/>
              <path d="M21 12H9"/>
            </svg>
          </span>
          <span class="text-sm font-medium">Logout</span>
        </button>
      </form>
    </div>

  </div>
</aside>
