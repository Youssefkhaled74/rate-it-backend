<!-- Filter Bar Component -->
@props([
    'filters' => [],
])

<div class="bg-[var(--surface)] border border-[var(--border)] rounded-2xl p-6 mb-8 shadow-[var(--shadow)]" 
     x-data="{ showFilters: false }">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <!-- Search -->
        <div class="md:col-span-2">
            <input type="text" 
                   name="search" 
                   placeholder="{{ session('rtl') ? 'بحث...' : 'Search...' }}"
                   value="{{ request('search') }}"
                   class="w-full px-4 py-2.5 rounded-xl bg-[var(--surface-2)] border border-[var(--border)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:outline-none focus:ring-2 focus:ring-[var(--brand)]" />
        </div>

        <!-- Quick Filters -->
        <div>
            <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-[var(--surface-2)] border border-[var(--border)] text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--brand)]">
                <option value="">{{ session('rtl') ? 'جميع الحالات' : 'All Status' }}</option>
                <option value="active" @selected(request('status') === 'active')>{{ session('rtl') ? 'نشط' : 'Active' }}</option>
                <option value="inactive" @selected(request('status') === 'inactive')>{{ session('rtl') ? 'غير نشط' : 'Inactive' }}</option>
            </select>
        </div>

        <!-- Actions -->
        <div class="flex gap-2">
            <button type="button" @click="showFilters = !showFilters" class="flex-1 px-4 py-2.5 rounded-xl bg-[var(--surface-2)] text-[var(--text-primary)] font-semibold hover:bg-[var(--surface-hover)] transition-colors">
                <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707l-5 5a1 1 0 00-.207.576v4.414a1 1 0 01-1.414.914l-3-2A1 1 0 015 13.414V8.707a1 1 0 00-.293-.707l-5-5A1 1 0 013 3z" clip-rule="evenodd"></path>
                </svg>
            </button>
            <button type="submit" class="flex-1 px-6 py-2.5 rounded-xl bg-[var(--brand)] text-white font-semibold hover:bg-red-700 transition-colors">
                {{ session('rtl') ? 'البحث' : 'Search' }}
            </button>
        </div>
    </div>

    <!-- Advanced Filters (Hidden by default) -->
    <div x-show="showFilters" class="pt-4 border-t border-[var(--border)] grid grid-cols-1 md:grid-cols-3 gap-4" x-transition>
        {{ $slot }}
    </div>
</div>
