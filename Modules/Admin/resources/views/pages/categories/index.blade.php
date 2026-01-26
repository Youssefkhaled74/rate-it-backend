@extends('admin::layouts.app')

@section('title', 'Categories')

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl md:text-4xl font-bold text-[var(--text-primary)]">
            {{ session('rtl') ? 'الفئات' : 'Categories' }}
        </h1>
        <p class="text-[var(--text-secondary)] mt-2">
            {{ session('rtl') ? 'إدارة فئات المنتجات' : 'Manage product categories' }}
        </p>
    </div>
    <x-admin::ui.button href="{{ route('admin.categories.create') }}" size="lg">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
        </svg>
        {{ session('rtl') ? 'فئة جديدة' : 'New Category' }}
    </x-admin::ui.button>
</div>

<!-- Filter Bar -->
<form method="GET" class="mb-8">
    <x-admin::forms.filter-bar>
        <x-admin::ui.dropdown 
            name="sort"
            label="{{ session('rtl') ? 'الترتيب' : 'Sort by' }}"
            :options="['created_at' => session('rtl') ? 'الأحدث' : 'Newest', 'name' => session('rtl') ? 'الاسم' : 'Name', 'status' => session('rtl') ? 'الحالة' : 'Status']"
            value="{{ request('sort') }}" />
    </x-admin::forms.filter-bar>
</form>

<!-- Bulk Actions Bar (shown when items selected) -->
<div x-data="{ selected: false }" class="mb-6 hidden" :class="{ hidden: !selected }">
    <div class="flex items-center justify-between bg-[var(--brand-lighter)] border border-[var(--brand)] rounded-2xl px-6 py-4">
        <div>
            <p class="text-sm font-semibold text-[var(--text-primary)]">
                <span x-text="selectedCount">0</span> {{ session('rtl') ? 'عنصر محدد' : 'items selected' }}
            </p>
        </div>
        <div class="flex gap-3">
            <button type="button" class="px-4 py-2 rounded-xl bg-[var(--surface-2)] text-[var(--text-primary)] font-semibold hover:bg-[var(--surface-hover)] transition-colors">
                {{ session('rtl') ? 'تفعيل' : 'Activate' }}
            </button>
            <button type="button" class="px-4 py-2 rounded-xl bg-[var(--surface-2)] text-[var(--text-primary)] font-semibold hover:bg-[var(--surface-hover)] transition-colors">
                {{ session('rtl') ? 'تعطيل' : 'Deactivate' }}
            </button>
            <button type="button" class="px-4 py-2 rounded-xl bg-[var(--danger)] text-white font-semibold hover:bg-red-700 transition-colors">
                {{ session('rtl') ? 'حذف' : 'Delete' }}
            </button>
        </div>
    </div>
</div>

<!-- Table Container -->
<x-admin::ui.card noPadding="true">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-[var(--surface-2)] border-b border-[var(--border)]">
                <tr>
                    <th class="px-6 py-4">
                        <input type="checkbox" class="rounded-lg cursor-pointer">
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[var(--text-secondary)] uppercase tracking-wide">
                        {{ session('rtl') ? 'الاسم' : 'Name' }}
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[var(--text-secondary)] uppercase tracking-wide">
                        {{ session('rtl') ? 'الصورة' : 'Image' }}
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[var(--text-secondary)] uppercase tracking-wide">
                        {{ session('rtl') ? 'الفئات الفرعية' : 'Subcategories' }}
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[var(--text-secondary)] uppercase tracking-wide">
                        {{ session('rtl') ? 'الحالة' : 'Status' }}
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[var(--text-secondary)] uppercase tracking-wide">
                        {{ session('rtl') ? 'تاريخ الإنشاء' : 'Created' }}
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[var(--text-secondary)] uppercase tracking-wide">
                        {{ session('rtl') ? 'الإجراءات' : 'Actions' }}
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-[var(--border)]">
                <!-- Sample rows - replace with @foreach loop -->
                @forelse ([ 
                    ['id' => 1, 'name' => 'Restaurants', 'image' => '🍽️', 'subcategories' => 8, 'status' => 'active', 'created' => '2024-01-15'],
                    ['id' => 2, 'name' => 'Cafés', 'image' => '☕', 'subcategories' => 5, 'status' => 'active', 'created' => '2024-01-14'],
                    ['id' => 3, 'name' => 'Shopping', 'image' => '🛍️', 'subcategories' => 12, 'status' => 'active', 'created' => '2024-01-13'],
                    ['id' => 4, 'name' => 'Hotels', 'image' => '🏨', 'subcategories' => 3, 'status' => 'inactive', 'created' => '2024-01-10'],
                ] as $category)
                    <tr class="hover:bg-[var(--surface-2)] transition-colors">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="rounded-lg cursor-pointer">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[var(--surface-2)] flex items-center justify-center text-lg">
                                    {{ $category['image'] }}
                                </div>
                                <span class="font-semibold text-[var(--text-primary)]">{{ $category['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <img src="https://via.placeholder.com/40" alt="{{ $category['name'] }}" class="w-10 h-10 rounded-lg object-cover">
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[var(--text-secondary)] font-medium">{{ $category['subcategories'] }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <x-admin::ui.badge 
                                :variant="$category['status'] === 'active' ? 'success' : 'warning'"
                                size="sm">
                                {{ $category['status'] === 'active' ? (session('rtl') ? 'نشط' : 'Active') : (session('rtl') ? 'غير نشط' : 'Inactive') }}
                            </x-admin::ui.badge>
                        </td>
                        <td class="px-6 py-4 text-sm text-[var(--text-secondary)]">
                            {{ \Carbon\Carbon::parse($category['created'])->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2" x-data="{ open: false }">
                                <x-admin::ui.button variant="ghost" size="sm" href="{{ route('admin.categories.edit', $category['id']) }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </x-admin::ui.button>
                                <button type="button" @click="open = !open" class="p-2 rounded-lg hover:bg-[var(--surface-2)] transition-colors">
                                    <svg class="w-4 h-4 text-[var(--text-secondary)]" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.5 1.5H9.5V.5h1v1zm0 17H9.5v1h1v-1zM19 10.5v-1h1v1h-1zM0 10.5v-1h1v1H0zm16.157-5.657l.707-.707.707.707-.707.707-.707-.707zm-11.314 11.314l-.707.707-.707-.707.707-.707.707.707zM19 1.5l-1.414-1.414 1.414 1.414zM1.5 19l-1.414-1.414L1.5 19z"></path>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-8 w-32 bg-[var(--surface)] border border-[var(--border)] rounded-xl shadow-lg overflow-hidden z-10" x-transition>
                                    <a href="#" class="block px-4 py-2.5 text-sm text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors">{{ session('rtl') ? 'عرض' : 'View' }}</a>
                                    <a href="{{ route('admin.categories.edit', $category['id']) }}" class="block px-4 py-2.5 text-sm text-[var(--text-primary)] hover:bg-[var(--surface-2)] transition-colors">{{ session('rtl') ? 'تحرير' : 'Edit' }}</a>
                                    <button type="button" class="w-full text-left px-4 py-2.5 text-sm text-[var(--danger)] hover:bg-[var(--surface-2)] transition-colors">{{ session('rtl') ? 'حذف' : 'Delete' }}</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12">
                            <x-admin::ui.empty-state 
                                title="{{ session('rtl') ? 'لا توجد فئات' : 'No Categories' }}"
                                description="{{ session('rtl') ? 'ابدأ بإنشاء فئة جديدة' : 'Start by creating a new category' }}"
                                actionLabel="{{ session('rtl') ? 'فئة جديدة' : 'New Category' }}"
                                actionHref="{{ route('admin.categories.create') }}" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    {{-- Replace with actual pagination --}}
    <div class="flex items-center justify-between px-6 py-4 bg-[var(--surface-2)] border-t border-[var(--border)] rounded-b-2xl">
        <div class="text-sm text-[var(--text-secondary)]">
            {{ session('rtl') ? 'يعرض' : 'Showing' }} 
            <span class="font-semibold">1</span> 
            {{ session('rtl') ? 'إلى' : 'to' }}
            <span class="font-semibold">4</span>
            {{ session('rtl') ? 'من' : 'of' }}
            <span class="font-semibold">4</span>
            {{ session('rtl') ? 'النتائج' : 'results' }}
        </div>
        <div class="flex items-center gap-2">
            <button disabled class="px-3 py-2 rounded-lg bg-[var(--surface-2)] text-[var(--text-tertiary)] cursor-not-allowed opacity-50">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            </button>
            <button disabled class="px-3 py-2 rounded-lg font-semibold bg-[var(--brand)] text-white">1</button>
            <button disabled class="px-3 py-2 rounded-lg bg-[var(--surface-2)] text-[var(--text-tertiary)] cursor-not-allowed opacity-50">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
</x-admin::ui.card>

<!-- Confirm Delete Modal -->
<x-admin::ui.confirm-delete 
    id="delete-category"
    title="{{ session('rtl') ? 'حذف الفئة' : 'Delete Category' }}"
    message="{{ session('rtl') ? 'هل أنت متأكد من رغبتك في حذف هذه الفئة؟ سيؤثر هذا على جميع الفئات الفرعية والمنتجات المرتبطة.' : 'Are you sure you want to delete this category? This will affect all related subcategories and products.' }}"
    itemName="Sample Category"
    action="#" />

@endsection
