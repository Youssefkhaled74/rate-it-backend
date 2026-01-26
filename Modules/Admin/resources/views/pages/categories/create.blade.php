@extends('admin::layouts.app')

@section('title', 'Create Category')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-bold text-[var(--text-primary)]">
        {{ session('rtl') ? 'فئة جديدة' : 'Create Category' }}
    </h1>
    <p class="text-[var(--text-secondary)] mt-2">
        {{ session('rtl') ? 'أضف فئة منتج جديدة للنظام' : 'Add a new product category to the system' }}
    </p>
</div>

<!-- Form Container -->
<form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="pb-32">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Basic Information -->
            <x-admin::ui.card>
                <h2 class="text-lg font-bold text-[var(--text-primary)] mb-6">
                    {{ session('rtl') ? 'المعلومات الأساسية' : 'Basic Information' }}
                </h2>

                <x-admin::forms.form-grid :columns="1">
                    <x-admin::ui.input 
                        name="name"
                        label="{{ session('rtl') ? 'اسم الفئة' : 'Category Name' }}"
                        type="text"
                        placeholder="{{ session('rtl') ? 'مثال: المطاعم' : 'e.g., Restaurants' }}"
                        value="{{ old('name') }}"
                        required />

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-[var(--text-primary)]">
                            {{ session('rtl') ? 'الوصف' : 'Description' }}
                        </label>
                        <textarea name="description" 
                                  placeholder="{{ session('rtl') ? 'أضف وصفاً للفئة...' : 'Add category description...' }}"
                                  rows="4"
                                  @class([
                                      'w-full px-4 py-2.5 rounded-xl bg-[var(--surface-2)] border transition-all duration-200 text-[var(--text-primary)] placeholder-[var(--text-tertiary)]',
                                      'focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2 focus:bg-[var(--surface)]',
                                      'border-[var(--danger)]' => $errors->has('description'),
                                      'border-[var(--border)]' => !$errors->has('description'),
                                  ])
                        >{{ old('description') }}</textarea>
                        @if ($errors->has('description'))
                            <p class="text-xs text-[var(--danger)] font-medium">{{ $errors->first('description') }}</p>
                        @endif
                    </div>
                </x-admin::forms.form-grid>
            </x-admin::ui.card>

            <!-- Image Upload -->
            <x-admin::ui.card>
                <h2 class="text-lg font-bold text-[var(--text-primary)] mb-6">
                    {{ session('rtl') ? 'الصورة' : 'Category Image' }}
                </h2>

                <div class="space-y-4">
                    <div class="border-2 border-dashed border-[var(--border)] rounded-2xl p-8 text-center cursor-pointer hover:border-[var(--brand)] hover:bg-[var(--brand-lighter)] transition-colors"
                         x-data="{ 
                             isDragging: false,
                             handleDrop(e) {
                                 e.preventDefault();
                                 this.isDragging = false;
                                 const files = e.dataTransfer.files;
                                 if (files.length) document.querySelector('input[type=file][name=image]').files = files;
                             }
                         }"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop="handleDrop($event)"
                         @click="document.querySelector('input[type=file][name=image]').click()">
                        <input type="file" 
                               name="image" 
                               accept="image/*" 
                               class="hidden"
                               @change="console.log($event.target.files)">
                        
                        <svg class="w-12 h-12 mx-auto mb-4 text-[var(--text-tertiary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        
                        <h3 class="font-semibold text-[var(--text-primary)]">
                            {{ session('rtl') ? 'اسحب الصورة هنا أو انقر للتحديث' : 'Drag image here or click to upload' }}
                        </h3>
                        <p class="text-sm text-[var(--text-tertiary)] mt-2">
                            {{ session('rtl') ? 'صيغة PNG أو JPG، حد أقصى 5 MB' : 'PNG or JPG, up to 5MB' }}
                        </p>
                    </div>

                    <!-- Image Preview -->
                    <div id="imagePreview" class="hidden">
                        <img id="previewImg" src="" alt="Preview" class="w-32 h-32 rounded-xl object-cover mx-auto">
                    </div>
                </div>
            </x-admin::ui.card>

            <!-- Additional Options -->
            <x-admin::ui.card>
                <h2 class="text-lg font-bold text-[var(--text-primary)] mb-6">
                    {{ session('rtl') ? 'خيارات إضافية' : 'Additional Options' }}
                </h2>

                <x-admin::forms.form-grid :columns="2">
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="w-5 h-5 rounded-lg border-[var(--border)] text-[var(--brand)] focus:ring-[var(--brand)]">
                            <span class="font-semibold text-[var(--text-primary)]">{{ session('rtl') ? 'تفعيل الفئة' : 'Activate Category' }}</span>
                        </label>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="featured" value="1" @checked(old('featured')) class="w-5 h-5 rounded-lg border-[var(--border)] text-[var(--brand)] focus:ring-[var(--brand)]">
                            <span class="font-semibold text-[var(--text-primary)]">{{ session('rtl') ? 'فئة مميزة' : 'Featured Category' }}</span>
                        </label>
                    </div>
                </x-admin::forms.form-grid>
            </x-admin::ui.card>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Preview Card -->
            <x-admin::ui.card class="sticky top-32">
                <h3 class="text-sm font-bold text-[var(--text-secondary)] uppercase tracking-wide mb-4">
                    {{ session('rtl') ? 'معاينة' : 'Preview' }}
                </h3>

                <div class="rounded-2xl overflow-hidden border border-[var(--border)]">
                    <div class="bg-gradient-to-br from-[var(--brand-lighter)] to-[var(--surface-2)] h-32 flex items-center justify-center">
                        <svg class="w-12 h-12 text-[var(--brand)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-[var(--text-primary)]">
                            <span x-data x-text="document.querySelector('input[name=name]')?.value || 'Category Name'"></span>
                        </h4>
                        <p class="text-xs text-[var(--text-tertiary)] mt-1 line-clamp-2">
                            <span x-data x-text="document.querySelector('textarea[name=description]')?.value || 'Category description will appear here'"></span>
                        </p>
                    </div>
                </div>

                <!-- Help Text -->
                <div class="mt-6 p-4 rounded-xl bg-[var(--surface-2)] border border-[var(--border)]">
                    <h4 class="text-sm font-semibold text-[var(--text-primary)] mb-2">
                        {{ session('rtl') ? 'نصائح' : 'Tips' }}
                    </h4>
                    <ul class="text-xs text-[var(--text-secondary)] space-y-2">
                        <li class="flex gap-2">
                            <span>•</span>
                            <span>{{ session('rtl') ? 'استخدم أسماء واضحة وموجزة' : 'Use clear and concise names' }}</span>
                        </li>
                        <li class="flex gap-2">
                            <span>•</span>
                            <span>{{ session('rtl') ? 'أضف صورة عالية الجودة' : 'Add high-quality image' }}</span>
                        </li>
                        <li class="flex gap-2">
                            <span>•</span>
                            <span>{{ session('rtl') ? 'اختر وصف معبر' : 'Choose descriptive text' }}</span>
                        </li>
                    </ul>
                </div>
            </x-admin::ui.card>
        </div>
    </div>

    <!-- Form Actions -->
    <x-admin::forms.form-actions 
        submitLabel="{{ session('rtl') ? 'إنشاء الفئة' : 'Create Category' }}"
        cancelHref="{{ route('admin.categories.index') }}" />
</form>

@endsection

@section('js')
<script>
    // Image preview handling
    document.querySelector('input[type=file][name=image]').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('previewImg').src = event.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
