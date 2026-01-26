<!-- Sidebar Navigation -->
<nav class="h-full flex flex-col" x-data="{ openSection: '' }">
    <!-- Logo -->
    <div class="px-6 py-8 border-b border-[var(--border)]">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-[var(--brand)] text-white flex items-center justify-center font-bold text-lg">
                R
            </div>
            <div class="hidden sm:block">
                <h1 class="text-lg font-bold text-[var(--text-primary)]">Rate It</h1>
                <p class="text-xs text-[var(--text-tertiary)]">Admin</p>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
        <!-- Dashboard -->
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[var(--brand-lighter)] text-[var(--brand)]' : '' }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Catalog Section -->
        <div x-data="{ open: {{ request()->routeIs('admin.categories*', 'admin.brands*', 'admin.subcategories*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                    <span class="font-medium">Catalog</span>
                </div>
                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </button>
            
            <!-- Submenu -->
            <div x-show="open" @click.away="open = false" class="ml-4 mt-2 space-y-1 pl-4 border-l border-[var(--border)]" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-75">
                <a href="#" class="block px-4 py-2 rounded-lg text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors {{ request()->routeIs('admin.categories*') ? 'text-[var(--brand)] font-semibold' : '' }}">Categories</a>
                <a href="#" class="block px-4 py-2 rounded-lg text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors {{ request()->routeIs('admin.subcategories*') ? 'text-[var(--brand)] font-semibold' : '' }}">Subcategories</a>
                <a href="#" class="block px-4 py-2 rounded-lg text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors {{ request()->routeIs('admin.brands*') ? 'text-[var(--brand)] font-semibold' : '' }}">Brands</a>
            </div>
        </div>

        <!-- Places & Branches -->
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors {{ request()->routeIs('admin.places*') ? 'bg-[var(--brand-lighter)] text-[var(--brand)]' : '' }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">Places</span>
        </a>

        <!-- Reviews & Ratings -->
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors {{ request()->routeIs('admin.reviews*') ? 'bg-[var(--brand-lighter)] text-[var(--brand)]' : '' }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            <span class="font-medium">Reviews</span>
        </a>

        <!-- QR Sessions -->
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors {{ request()->routeIs('admin.qr*') ? 'bg-[var(--brand-lighter)] text-[var(--brand)]' : '' }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM4 15a1 1 0 00-1 1v2a1 1 0 001 1h5a1 1 0 001-1v-2a1 1 0 00-1-1H4z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">QR Sessions</span>
        </a>

        <!-- Users & Loyalty -->
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors {{ request()->routeIs('admin.users*') ? 'bg-[var(--brand-lighter)] text-[var(--brand)]' : '' }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 6a9 9 0 100 18 9 9 0 000-18zm7.712 9.21c-.385.426-.885.631-1.41.568.503.591.933 1.288 1.159 2.06.147.573.355 1.153.535 1.68.075.213.076.469.004.676-.073.208-.222.381-.437.488-.215.107-.484.117-.706.03-.223-.087-.43-.272-.572-.52l-.011-.02c-.196-.351-.454-.837-.776-1.248-.322.411-.58.897-.776 1.248l-.011.02c-.142.248-.349.433-.572.52-.222.087-.491.077-.706-.03-.215-.107-.364-.28-.437-.488-.072-.207-.071-.463.004-.676.18-.527.388-1.107.535-1.68.226-.772.656-1.469 1.159-2.06-.525.063-1.025-.142-1.41-.568-.385-.427-.622-.99-.622-1.588 0-.598.237-1.161.622-1.588.385-.427.885-.632 1.41-.569-.503-.59-.933-1.287-1.159-2.06-.147-.573-.355-1.153-.535-1.68-.075-.212-.076-.468-.004-.675.073-.208.222-.381.437-.488.215-.107.484-.118.706-.031.223.087.43.272.572.52l.011.02c.196.35.454.836.776 1.248.322-.412.58-.898.776-1.249l.011-.02c.142-.248.349-.433.572-.52.222-.087.491-.077.706.031.215.107.364.28.437.488.072.207.071.463-.004.675-.18.527-.388 1.107-.535 1.68-.226.773-.656 1.47-1.159 2.06.525-.063 1.025.142 1.41.569z"></path>
            </svg>
            <span class="font-medium">Users</span>
        </a>

        <!-- Settings Section -->
        <div class="pt-4 border-t border-[var(--border)]" x-data="{ open: {{ request()->routeIs('admin.settings*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Settings</span>
                </div>
                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </button>
            
            <div x-show="open" class="ml-4 mt-2 space-y-1 pl-4 border-l border-[var(--border)]" x-transition>
                <a href="#" class="block px-4 py-2 rounded-lg text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors">Roles & Permissions</a>
                <a href="#" class="block px-4 py-2 rounded-lg text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors">Audit Logs</a>
            </div>
        </div>
    </div>

    <!-- Bottom Actions -->
    <div class="border-t border-[var(--border)] px-4 py-6 space-y-3">
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium text-sm">Help & Support</span>
        </a>
        
        <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <span class="font-medium text-sm">Logout</span>
        </button>
    </div>
</nav>
