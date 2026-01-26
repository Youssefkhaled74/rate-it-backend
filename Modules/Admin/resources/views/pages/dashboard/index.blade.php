@extends('admin::layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl md:text-4xl font-bold text-[var(--text-primary)]">
            {{ session('rtl') ? 'لوحة التحكم' : 'Dashboard' }}
        </h1>
        <p class="text-[var(--text-secondary)] mt-2">
            {{ session('rtl') ? 'مرحباً بك، مسؤول النظام' : 'Welcome back, Admin' }}
        </p>
    </div>
    <x-admin::ui.button href="#" size="lg">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"></path>
        </svg>
        {{ session('rtl') ? 'تحميل بيانات' : 'Export Data' }}
    </x-admin::ui.button>
</div>

<!-- KPI Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <x-admin::ui.stat-card 
        title="{{ session('rtl') ? 'إجمالي المستخدمين' : 'Total Users' }}"
        value="12,847"
        subtitle="{{ session('rtl') ? 'مستخدم نشط' : 'active users' }}"
        :trend="['value' => 12, 'positive' => true]"
        icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 6a3 3 0 11-6 0 3 3 0 016 0zM9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM14.707 12.707a1 1 0 00-1.414-1.414l-2.829 2.829-1.414-1.414a1 1 0 00-1.414 1.414l2.828 2.829a1 1 0 001.414 0l4.243-4.243z"></path></svg>'
    />

    <x-admin::ui.stat-card 
        title="{{ session('rtl') ? 'إجمالي المراجعات' : 'Total Reviews' }}"
        value="3,462"
        subtitle="{{ session('rtl') ? 'هذا الشهر' : 'this month' }}"
        :trend="['value' => 8, 'positive' => true]"
        icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>'
    />

    <x-admin::ui.stat-card 
        title="{{ session('rtl') ? 'الأماكن النشطة' : 'Active Places' }}"
        value="856"
        subtitle="{{ session('rtl') ? 'مع تقييمات' : 'with ratings' }}"
        :trend="['value' => 5, 'positive' => true]"
        icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>'
    />

    <x-admin::ui.stat-card 
        title="{{ session('rtl') ? 'إيرادات الاشتراك' : 'Subscription Revenue' }}"
        value="$24,580"
        subtitle="{{ session('rtl') ? 'هذا الشهر' : 'this month' }}"
        :trend="['value' => 15, 'positive' => true]"
        icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>'
    />
</div>

<!-- Charts & Activity Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Chart Placeholder -->
    <div class="lg:col-span-2">
        <x-admin::ui.card>
            <div class="mb-6">
                <h3 class="text-lg font-bold text-[var(--text-primary)]">
                    {{ session('rtl') ? 'نشاط المراجعات' : 'Reviews Activity' }}
                </h3>
                <p class="text-sm text-[var(--text-tertiary)] mt-1">
                    {{ session('rtl') ? 'آخر 30 يوم' : 'Last 30 days' }}
                </p>
            </div>
            
            <!-- Chart Skeleton (Replace with actual chart library) -->
            <div class="h-64 bg-[var(--surface-2)] rounded-xl flex items-end justify-between p-4 gap-1">
                @for ($i = 1; $i <= 12; $i++)
                    <div class="flex-1 bg-[var(--brand)] rounded-t-lg opacity-70 hover:opacity-100 transition-opacity"
                         style="height: {{ rand(30, 90) }}%;"></div>
                @endfor
            </div>
        </x-admin::ui.card>
    </div>

    <!-- Recent Activity -->
    <div>
        <x-admin::ui.card>
            <h3 class="text-lg font-bold text-[var(--text-primary)] mb-6">
                {{ session('rtl') ? 'النشاط الأخير' : 'Recent Activity' }}
            </h3>
            
            <div class="space-y-4">
                @foreach ([
                    ['type' => 'review', 'text' => session('rtl') ? 'تقييم جديد على مطعم الريف' : 'New review on Al Reef Restaurant', 'time' => '2 minutes ago'],
                    ['type' => 'user', 'text' => session('rtl') ? 'مستخدم جديد: أحمد محمد' : 'New user: Ahmed Mohammad', 'time' => '1 hour ago'],
                    ['type' => 'place', 'text' => session('rtl') ? 'مكان جديد مسجل' : 'New place registered', 'time' => '3 hours ago'],
                    ['type' => 'subscription', 'text' => session('rtl') ? 'اشتراك جديد - خطة بريميوم' : 'New subscription - Premium Plan', 'time' => '5 hours ago'],
                ] as $activity)
                    <div class="flex gap-3 pb-4 border-b border-[var(--border)] last:border-b-0 last:pb-0">
                        <div class="w-10 h-10 rounded-lg flex-shrink-0 {{ 
                            $activity['type'] === 'review' ? 'bg-[var(--brand-lighter)] text-[var(--brand)]' :
                            ($activity['type'] === 'user' ? 'bg-[var(--info-light)] text-[var(--info)]' :
                            ($activity['type'] === 'place' ? 'bg-[var(--success-light)] text-[var(--success)]' :
                            'bg-[var(--warning-light)] text-[var(--warning)]'))
                        }} flex items-center justify-center">
                            @if ($activity['type'] === 'review')
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            @elseif ($activity['type'] === 'user')
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 6a3 3 0 11-6 0 3 3 0 016 0zM9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM14.707 12.707a1 1 0 00-1.414-1.414l-2.829 2.829-1.414-1.414a1 1 0 00-1.414 1.414l2.828 2.829a1 1 0 001.414 0l4.243-4.243z"></path></svg>
                            @elseif ($activity['type'] === 'place')
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                            @else
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $activity['text'] }}</p>
                            <p class="text-xs text-[var(--text-tertiary)] mt-1">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-admin::ui.card>
    </div>
</div>

<!-- Quick Actions -->
<div>
    <h3 class="text-lg font-bold text-[var(--text-primary)] mb-4">
        {{ session('rtl') ? 'إجراءات سريعة' : 'Quick Actions' }}
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-admin::ui.card clickable="true" hoverable="true" class="text-center">
            <div class="w-12 h-12 rounded-2xl bg-[var(--brand-lighter)] text-[var(--brand)] flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"></path></svg>
            </div>
            <h4 class="font-semibold text-[var(--text-primary)]">{{ session('rtl') ? 'فئة جديدة' : 'New Category' }}</h4>
            <p class="text-xs text-[var(--text-tertiary)] mt-2">{{ session('rtl') ? 'إضافة فئة منتج' : 'Add product category' }}</p>
        </x-admin::ui.card>

        <x-admin::ui.card clickable="true" hoverable="true" class="text-center">
            <div class="w-12 h-12 rounded-2xl bg-[var(--info-light)] text-[var(--info)] flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
            </div>
            <h4 class="font-semibold text-[var(--text-primary)]">{{ session('rtl') ? 'مكان جديد' : 'New Place' }}</h4>
            <p class="text-xs text-[var(--text-tertiary)] mt-2">{{ session('rtl') ? 'تسجيل موقع جديد' : 'Register new location' }}</p>
        </x-admin::ui.card>

        <x-admin::ui.card clickable="true" hoverable="true" class="text-center">
            <div class="w-12 h-12 rounded-2xl bg-[var(--success-light)] text-[var(--success)] flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
            </div>
            <h4 class="font-semibold text-[var(--text-primary)]">{{ session('rtl') ? 'عرض التقييمات' : 'View Reviews' }}</h4>
            <p class="text-xs text-[var(--text-tertiary)] mt-2">{{ session('rtl') ? 'إدارة التقييمات' : 'Manage reviews' }}</p>
        </x-admin::ui.card>

        <x-admin::ui.card clickable="true" hoverable="true" class="text-center">
            <div class="w-12 h-12 rounded-2xl bg-[var(--warning-light)] text-[var(--warning)] flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
            </div>
            <h4 class="font-semibold text-[var(--text-primary)]">{{ session('rtl') ? 'الاشتراكات' : 'Subscriptions' }}</h4>
            <p class="text-xs text-[var(--text-tertiary)] mt-2">{{ session('rtl') ? 'إدارة الاشتراكات' : 'Manage plans' }}</p>
        </x-admin::ui.card>
    </div>
</div>

@endsection
