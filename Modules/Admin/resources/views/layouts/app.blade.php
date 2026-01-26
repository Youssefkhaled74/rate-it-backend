<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ session('admin_locale', 'ar') === 'ar' ? 'rtl' : 'ltr' }}" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Rate It</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite Assets (CSS + Alpine.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}">
    @yield('css')
    
    <!-- Theme & Direction Script (inline to prevent FOUC) -->
    <script>
        (function() {
            const locale = '{{ session("admin_locale", "ar") }}';
            const theme = localStorage.getItem('admin_theme') || 'light';
            const dir = locale === 'ar' ? 'rtl' : 'ltr';
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.setAttribute('dir', dir);
            document.documentElement.lang = locale;
        })();
    </script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-[var(--bg)]">
        <!-- Sidebar -->
        <aside class="fixed left-0 top-0 bottom-0 w-64 bg-[var(--surface)] border-r border-[var(--border)] z-40 hidden lg:block overflow-y-auto"
               x-data="{ collapsed: false }"
               @keydown.escape="$dispatch('sidebar-close')">
            @include('admin::partials.sidebar')
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"
             x-show="sidebarOpen"
             @click="sidebarOpen = false"
             x-transition></div>

        <!-- Mobile Sidebar Drawer -->
        <aside class="fixed left-0 top-0 bottom-0 w-64 bg-[var(--surface)] border-r border-[var(--border)] z-40 lg:hidden transform transition-transform duration-300"
               x-show="sidebarOpen"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               x-ref="mobileSidebar">
            @include('admin::partials.sidebar')
        </aside>

        <!-- Main Content -->
        <div class="lg:ml-64">
            <!-- Topbar -->
            @include('admin::partials.topbar')

            <!-- Page Content -->
            <main class="pt-24 px-4 md:px-8 pb-8 max-w-full">
                <!-- Breadcrumbs -->
                @include('admin::partials.breadcrumbs')

                <!-- Flash Messages -->
                @include('admin::partials.flash-messages')

                <!-- Page Content Slot -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Main App Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('app', () => ({
                sidebarOpen: false,
                notificationsOpen: false,
                profileMenuOpen: false,
                
                init() {
                    // Close sidebar on large screens
                    window.addEventListener('resize', () => {
                        if (window.innerWidth >= 1024) {
                            this.sidebarOpen = false;
                        }
                    });
                },
                
                toggleTheme() {
                    const newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('admin_theme', newTheme);
                    this.$dispatch('theme-changed', { theme: newTheme });
                },
                
                toggleDirection() {
                    const newDir = document.documentElement.getAttribute('dir') === 'rtl' ? 'ltr' : 'rtl';
                    document.documentElement.setAttribute('dir', newDir);
                    document.documentElement.lang = newDir === 'rtl' ? 'ar' : 'en';
                    localStorage.setItem('admin_direction', newDir);
                    this.$dispatch('direction-changed', { direction: newDir });
                },
                
                closeMenus() {
                    this.notificationsOpen = false;
                    this.profileMenuOpen = false;
                }
            }));
        });
    </script>
    
    @yield('js')
</body>
</html>
