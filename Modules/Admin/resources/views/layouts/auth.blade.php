<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ session('admin_locale', 'ar') === 'ar' ? 'rtl' : 'ltr' }}" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Rate It</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Development - CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js (Development - CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}">
    @yield('css')
    
    <!-- Theme Script (inline to prevent FOUC) -->
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
<body class="font-sans antialiased bg-[var(--bg-secondary)]">
    <div x-data="{ theme: 'light', rtl: false }" @alpine:init="theme = document.documentElement.getAttribute('data-theme'); rtl = document.documentElement.getAttribute('dir') === 'rtl'">
        <!-- Minimal Topbar for Auth Pages -->
        <header class="fixed top-0 left-0 right-0 bg-[var(--surface)] border-b border-[var(--border)] z-50">
            <div class="max-w-md mx-auto px-4 py-4 flex items-center justify-between">
                <div class="text-xl font-bold text-[var(--brand)]">Rate It</div>
                <div class="flex gap-2">
                    <!-- Theme Toggle -->
                    <button @click="theme = theme === 'dark' ? 'light' : 'dark'; 
                                      document.documentElement.setAttribute('data-theme', theme);
                                      localStorage.setItem('admin_theme', theme);"
                            class="p-2 rounded-lg hover:bg-[var(--surface-2)] transition-colors">
                        <template x-if="theme === 'light'">
                            <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                        </template>
                        <template x-if="theme === 'dark'">
                            <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.828-2.828a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm.464-4.536l.707-.707a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414zm-2.828 2.828a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM13 11a1 1 0 110 2h-1a1 1 0 110-2h1zm4 0a1 1 0 110 2h-1a1 1 0 110-2h1zM9 18a1 1 0 01-1-1v-1a1 1 0 112 0v1a1 1 0 01-1 1zM5.05 4.05a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707zm9.9 9.9a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707zM4 11a1 1 0 110 2H3a1 1 0 110-2h1z" clip-rule="evenodd"></path>
                            </svg>
                        </template>
                    </button>
                    <!-- Direction Toggle -->
                    <button @click="rtl = !rtl;
                                    document.documentElement.setAttribute('dir', rtl ? 'rtl' : 'ltr');
                                    document.documentElement.lang = rtl ? 'ar' : 'en';
                                    localStorage.setItem('admin_direction', rtl ? 'rtl' : 'ltr');"
                            class="px-3 py-2 rounded-lg text-xs font-semibold bg-[var(--surface-2)] text-[var(--text-primary)] hover:bg-[var(--surface-hover)] transition-colors"
                            x-text="rtl ? 'EN' : 'AR'"></button>
                </div>
            </div>
        </header>

        <!-- Auth Content -->
        <div class="min-h-screen flex items-center justify-center pt-20 pb-8">
            <div class="w-full max-w-md">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @yield('js')
</body>
</html>
