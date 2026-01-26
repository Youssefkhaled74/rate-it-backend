<!-- Confirm Delete Modal Component -->
@props([
    'id' => 'confirm-delete',
    'title' => 'Delete Item',
    'message' => 'Are you sure you want to delete this item? This action cannot be undone.',
    'itemName' => null,
    'confirmText' => 'Delete',
    'cancelText' => 'Cancel',
    'action' => '#',
    'method' => 'DELETE',
])

<div x-data="{ open: false, typing: '', required: false }" @{{ $id }}-open.window="open = true; typing = ''" @{{ $id }}-close.window="open = false">
    <!-- Backdrop -->
    <div x-show="open" x-transition class="fixed inset-0 bg-black/50 z-40" @click="open = false"></div>

    <!-- Modal -->
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="open = false">
        <div class="bg-[var(--surface)] rounded-2xl shadow-lg max-w-md w-full" @click.stop>
            <!-- Header -->
            <div class="px-6 py-4 border-b border-[var(--border)]">
                <h2 class="text-lg font-bold text-[var(--danger)]">{{ $title }}</h2>
            </div>

            <!-- Body -->
            <div class="px-6 py-6 space-y-4">
                <p class="text-[var(--text-secondary)]">{{ $message }}</p>
                
                @if ($itemName)
                    <div class="p-4 rounded-xl bg-[var(--surface-2)] border border-[var(--border)]">
                        <p class="text-xs text-[var(--text-tertiary)] font-semibold">{{ session('rtl') ? 'سيتم حذف:' : 'Will delete:' }}</p>
                        <p class="text-[var(--text-primary)] font-semibold mt-1">{{ $itemName }}</p>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-sm text-[var(--text-secondary)]">
                            {{ session('rtl') ? 'اكتب اسم العنصر للتأكيد:' : 'Type the item name to confirm:' }}
                        </label>
                        <input type="text" 
                               x-model="typing" 
                               placeholder="{{ $itemName }}"
                               class="w-full px-4 py-2.5 rounded-xl bg-[var(--surface-2)] border border-[var(--border)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:outline-none focus:ring-2 focus:ring-[var(--danger)]" />
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-[var(--border)] flex gap-3 justify-end">
                <button @click="open = false" 
                        class="px-4 py-2.5 rounded-xl bg-[var(--surface-2)] text-[var(--text-primary)] font-semibold hover:bg-[var(--surface-hover)] transition-colors">
                    {{ $cancelText }}
                </button>
                
                <form method="POST" action="{{ $action }}" class="inline">
                    @csrf
                    @method($method)
                    <button type="submit" 
                            @if ($itemName)
                                :disabled="typing !== '{{ $itemName }}'"
                                :class="typing !== '{{ $itemName }}' ? 'opacity-50 cursor-not-allowed' : ''"
                            @endif
                            class="px-4 py-2.5 rounded-xl bg-[var(--danger)] text-white font-semibold hover:bg-red-700 transition-colors">
                        {{ $confirmText }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
