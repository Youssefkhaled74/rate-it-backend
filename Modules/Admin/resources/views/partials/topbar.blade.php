<!-- Top Navigation Bar -->
<header class="fixed top-0 left-0 lg:left-64 right-0 bg-[var(--surface)] border-b border-[var(--border)] z-30 h-20">
    <div class="h-full px-4 md:px-8 flex items-center justify-between" x-data="{ open: false }">
        <!-- Mobile Menu Toggle & Search -->
        <div class="flex items-center gap-4 flex-1">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg hover:bg-[var(--surface-2)] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Search Bar (Desktop) -->
            <div class="hidden md:flex flex-1 max-w-xs">
                <div class="w-full relative">
                    <input type="text" placeholder="{{ session('rtl') ? 'بحث...' : 'Search...' }}" 
                           class="w-full px-4 py-2.5 pr-10 rounded-xl bg-[var(--surface-2)] border border-[var(--border)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2 transition-all" />
                    <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-[var(--text-tertiary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Right Actions -->
        <div class="flex items-center gap-4">
            <!-- Language Toggle -->
            <button @click="document.documentElement.setAttribute('dir', document.documentElement.getAttribute('dir') === 'rtl' ? 'ltr' : 'rtl');
                            document.documentElement.lang = document.documentElement.getAttribute('dir') === 'rtl' ? 'ar' : 'en';
                            localStorage.setItem('admin_direction', document.documentElement.getAttribute('dir'));"
                    class="px-3 py-2 rounded-lg text-xs font-semibold text-[var(--text-primary)] bg-[var(--surface-2)] hover:bg-[var(--surface-hover)] transition-colors">
                {{ session('rtl') ? 'EN' : 'AR' }}
            </button>

            <!-- Theme Toggle -->
            <button @click="document.documentElement.setAttribute('data-theme', document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
                            localStorage.setItem('admin_theme', document.documentElement.getAttribute('data-theme'));"
                    class="p-2 rounded-lg hover:bg-[var(--surface-2)] transition-colors">
                <template x-if="document.documentElement.getAttribute('data-theme') === 'light'">
                    <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                </template>
                <template x-if="document.documentElement.getAttribute('data-theme') === 'dark'">
                    <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.828-2.828a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm.464-4.536l.707-.707a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414zm-2.828 2.828a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM13 11a1 1 0 110 2h-1a1 1 0 110-2h1zm4 0a1 1 0 110 2h-1a1 1 0 110-2h1zM9 18a1 1 0 01-1-1v-1a1 1 0 112 0v1a1 1 0 01-1 1zM5.05 4.05a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707zm9.9 9.9a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707zM4 11a1 1 0 110 2H3a1 1 0 110-2h1z" clip-rule="evenodd"></path>
                    </svg>
                </template>
            </button>

            <!-- Notifications -->
            <div class="relative" @click.away="notificationsOpen = false">
                <button @click="notificationsOpen = !notificationsOpen" class="relative p-2 rounded-lg hover:bg-[var(--surface-2)] transition-colors">
                    <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-[var(--brand)] rounded-full"></span>
                </button>

                <!-- Notifications Dropdown -->
                <div x-show="notificationsOpen" x-transition class="absolute right-0 mt-2 w-80 bg-[var(--surface)] border border-[var(--border)] rounded-2xl shadow-lg p-4 space-y-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-[var(--text-primary)]">{{ session('rtl') ? 'التنبيهات' : 'Notifications' }}</h3>
                        <button class="text-xs text-[var(--text-tertiary)] hover:text-[var(--text-secondary)]">{{ session('rtl') ? 'مسح الكل' : 'Clear all' }}</button>
                    </div>
                    
                    <!-- Notification Items -->
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        <div class="p-3 rounded-lg bg-[var(--surface-2)] border border-[var(--border)]">
                            <p class="text-sm font-medium text-[var(--text-primary)]">New review posted</p>
                            <p class="text-xs text-[var(--text-tertiary)] mt-1">Al Reef Restaurant - 5 stars</p>
                            <p class="text-xs text-[var(--text-tertiary)] mt-1">2 minutes ago</p>
                        </div>
                        <div class="p-3 rounded-lg bg-[var(--surface-2)] border border-[var(--border)]">
                            <p class="text-sm font-medium text-[var(--text-primary)]">Subscription payment received</p>
                            <p class="text-xs text-[var(--text-tertiary)] mt-1">Premium Plan - $29.99</p>
                            <p class="text-xs text-[var(--text-tertiary)] mt-1">5 minutes ago</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Menu -->
            <div class="relative" @click.away="profileMenuOpen = false">
                <button @click="profileMenuOpen = !profileMenuOpen" class="flex items-center gap-2 p-2 rounded-lg hover:bg-[var(--surface-2)] transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-[var(--brand)] text-white flex items-center justify-center font-semibold text-sm">
                        A
                    </div>
                    <svg :class="profileMenuOpen ? 'rotate-180' : ''" class="w-4 h-4 text-[var(--text-tertiary)] transition-transform hidden md:block" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <!-- Profile Dropdown -->
                <div x-show="profileMenuOpen" x-transition class="absolute right-0 mt-2 w-56 bg-[var(--surface)] border border-[var(--border)] rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-4 py-4 border-b border-[var(--border)]">
                        <p class="text-sm font-semibold text-[var(--text-primary)]">Admin User</p>
                        <p class="text-xs text-[var(--text-tertiary)]">admin@rateit.com</p>
                    </div>
                    
                    <div class="p-2 space-y-1">
                        <a href="#" class="block px-4 py-2.5 rounded-lg text-sm text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors">{{ session('rtl') ? 'الملف الشخصي' : 'My Profile' }}</a>
                        <a href="#" class="block px-4 py-2.5 rounded-lg text-sm text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors">{{ session('rtl') ? 'الإعدادات' : 'Settings' }}</a>
                    </div>
                    
                    <div class="border-t border-[var(--border)] p-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2.5 rounded-lg text-sm text-[var(--danger)] hover:bg-[var(--surface-2)] transition-colors">
                                {{ session('rtl') ? 'تسجيل الخروج' : 'Logout' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
