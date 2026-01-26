<!-- Flash Messages -->
<div class="space-y-4 mb-8" x-data="{ 
    messages: {
        success: @json(session('success')),
        error: @json(session('error')),
        warning: @json(session('warning')),
        info: @json(session('info'))
    }
}" x-init="Object.values(messages).some(m => m) && setTimeout(() => { Object.keys(messages).forEach(k => messages[k] && (messages[k] = null)) }, 6000)">
    
    <!-- Success Message -->
    <template x-if="messages.success">
        <div class="px-6 py-4 rounded-2xl bg-[var(--success-light)] border border-[var(--success)] flex items-start gap-4 animate-in slide-in-from-top-2"
             x-transition:enter="transition ease-out duration-300"
             x-transition:leave="transition ease-in duration-300">
            <svg class="w-5 h-5 text-[var(--success)] flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1">
                <h3 class="font-semibold text-[var(--text-primary)]">{{ session('rtl') ? 'نجح' : 'Success' }}</h3>
                <p class="text-sm text-[var(--text-secondary)] mt-1" x-text="messages.success"></p>
            </div>
            <button @click="messages.success = null" class="text-[var(--text-tertiary)] hover:text-[var(--text-secondary)] flex-shrink-0">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </template>

    <!-- Error Message -->
    <template x-if="messages.error">
        <div class="px-6 py-4 rounded-2xl bg-[var(--danger-light)] border border-[var(--danger)] flex items-start gap-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:leave="transition ease-in duration-300">
            <svg class="w-5 h-5 text-[var(--danger)] flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1">
                <h3 class="font-semibold text-[var(--text-primary)]">{{ session('rtl') ? 'خطأ' : 'Error' }}</h3>
                <p class="text-sm text-[var(--text-secondary)] mt-1" x-text="messages.error"></p>
            </div>
            <button @click="messages.error = null" class="text-[var(--text-tertiary)] hover:text-[var(--text-secondary)] flex-shrink-0">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </template>

    <!-- Warning Message -->
    <template x-if="messages.warning">
        <div class="px-6 py-4 rounded-2xl bg-[var(--warning-light)] border border-[var(--warning)] flex items-start gap-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:leave="transition ease-in duration-300">
            <svg class="w-5 h-5 text-[var(--warning)] flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1">
                <h3 class="font-semibold text-[var(--text-primary)]">{{ session('rtl') ? 'تحذير' : 'Warning' }}</h3>
                <p class="text-sm text-[var(--text-secondary)] mt-1" x-text="messages.warning"></p>
            </div>
            <button @click="messages.warning = null" class="text-[var(--text-tertiary)] hover:text-[var(--text-secondary)] flex-shrink-0">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </template>

    <!-- Info Message -->
    <template x-if="messages.info">
        <div class="px-6 py-4 rounded-2xl bg-[var(--info-light)] border border-[var(--info)] flex items-start gap-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:leave="transition ease-in duration-300">
            <svg class="w-5 h-5 text-[var(--info)] flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1">
                <h3 class="font-semibold text-[var(--text-primary)]">{{ session('rtl') ? 'معلومة' : 'Info' }}</h3>
                <p class="text-sm text-[var(--text-secondary)] mt-1" x-text="messages.info"></p>
            </div>
            <button @click="messages.info = null" class="text-[var(--text-tertiary)] hover:text-[var(--text-secondary)] flex-shrink-0">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </template>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="px-6 py-4 rounded-2xl bg-[var(--danger-light)] border border-[var(--danger)]"
             x-transition:enter="transition ease-out duration-300"
             x-transition:leave="transition ease-in duration-300">
            <h3 class="font-semibold text-[var(--text-primary)] mb-2">{{ session('rtl') ? 'يرجى التحقق من الأخطاء التالية:' : 'Please check the following errors:' }}</h3>
            <ul class="list-disc list-inside text-sm text-[var(--text-secondary)] space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
