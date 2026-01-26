# Rate It Admin UI Kit - Quick Reference

## 🎨 Colors

```blade
<!-- Primary (Red Brand) -->
bg-[var(--brand)]          <!-- #dc2626 light, #ef4444 dark -->
text-[var(--brand)]
border-[var(--brand)]

<!-- Success Green -->
bg-[var(--success)]        <!-- #10b981 -->

<!-- Warning Amber -->
bg-[var(--warning)]        <!-- #f59e0b -->

<!-- Danger Red -->
bg-[var(--danger)]         <!-- #ef4444 -->

<!-- Info Blue -->
bg-[var(--info)]           <!-- #3b82f6 -->
```

## 📐 Spacing

```
Gap/Padding Sizes:
- px-2 = 8px
- px-3 = 12px  
- px-4 = 16px (default)
- px-6 = 24px (cards)
- px-8 = 32px (sections)

Use: gap-4 between items, px-6 for card padding
```

## 🎯 Components Cheat Sheet

### Card
```blade
<x-admin::ui.card hoverable="true">
  Content
</x-admin::ui.card>
```

### Button
```blade
<x-admin::ui.button variant="primary" size="md" href="#">
  Button Text
</x-admin::ui.button>

Variants: primary, secondary, ghost, danger
Sizes: sm, md, lg
```

### Input
```blade
<x-admin::ui.input 
  name="field"
  label="Label"
  type="text"
  value="{{ old('field') }}"
  required />
```

### Badge
```blade
<x-admin::ui.badge variant="success">Active</x-admin::ui.badge>

Variants: success, warning, danger, info, neutral
```

### Modal
```blade
<x-admin::ui.modal id="my-modal" title="Title">
  Content
</x-admin::ui.modal>

Trigger: <button @click="$dispatch('my-modal-open')">Open</button>
Close: <button @click="$dispatch('my-modal-close')">Close</button>
```

### Table
```blade
<x-admin::ui.table :headers="['Name', 'Email', 'Status']">
  @foreach ($items as $item)
    <tr>
      <td>{{ $item->name }}</td>
      <td>{{ $item->email }}</td>
      <td><x-admin::ui.badge>Active</x-admin::ui.badge></td>
    </tr>
  @endforeach
</x-admin::ui.table>
```

### Empty State
```blade
<x-admin::ui.empty-state 
  title="No data"
  description="Start by creating an item"
  actionLabel="Create"
  actionHref="{{ route('items.create') }}" />
```

### Pagination
```blade
<x-admin::ui.pagination :paginator="$items" />
```

### Filter Bar
```blade
<form>
  <x-admin::forms.filter-bar>
    <!-- Additional filters -->
  </x-admin::forms.filter-bar>
</form>
```

### Form Grid
```blade
<x-admin::forms.form-grid :columns="2">
  <x-admin::ui.input name="first_name" label="First Name" />
  <x-admin::ui.input name="last_name" label="Last Name" />
</x-admin::forms.form-grid>
```

### Form Actions (Sticky Footer)
```blade
<x-admin::forms.form-actions 
  submitLabel="Save"
  cancelHref="{{ route('items.index') }}" />
```

### Skeleton Loader
```blade
<x-admin::ui.skeleton type="row" count="5" />
<!-- Types: row, card, line -->
```

### Toast
```blade
<x-admin::ui.toast 
  variant="success"
  message="Success!"
  duration="5000" />

<!-- Or via JavaScript -->
<script>AdminUI.toast('Message', 'success');</script>
```

### Confirm Delete
```blade
<x-admin::ui.confirm-delete 
  id="delete-item"
  title="Delete?"
  message="Are you sure?"
  itemName="{{ $item->name }}"
  action="{{ route('items.destroy', $item) }}" />

Trigger: <button @click="$dispatch('delete-item-open')">Delete</button>
```

## 🌓 Theme & Direction

```javascript
// Toggle theme
document.documentElement.setAttribute('data-theme', 'dark');
localStorage.setItem('admin_theme', 'dark');

// Toggle RTL/LTR
document.documentElement.setAttribute('dir', 'rtl');
localStorage.setItem('admin_direction', 'rtl');

// Check current
AdminUI.isDarkMode();  // true/false
AdminUI.isRTL();       // true/false
```

## 🎛️ Alpine.js Utilities

```javascript
AdminUI.toast(message, type, duration)  // Show toast
AdminUI.copyToClipboard(text)           // Copy to clipboard
AdminUI.formatCurrency(amount)          // Format as currency
AdminUI.formatDate(date)                // Format date
AdminUI.confirm(msg, onConfirm)         // Confirm dialog
AdminUI.debounce(fn, delay)             // Debounce function
```

## 🎯 Alpine Directives

```html
<!-- Data binding -->
x-data="{ count: 0 }"

<!-- Event handling -->
@click="count++"
@submit.prevent="..."
@keydown.escape="..."

<!-- Conditionals -->
x-show="open"
x-if="condition"

<!-- Loops -->
x-for="item in items"

<!-- Classes -->
:class="{ active: isActive }"
@class(["px-4", "py-2", condition ? "active" : ""])

<!-- Text/HTML -->
x-text="message"
x-html="message"

<!-- Transitions -->
x-transition:enter="transition ease-out duration-300"
x-transition:leave="transition ease-in duration-150"
```

## 📱 Responsive Breakpoints

```
md:    640px   (tablets)
lg:    1024px  (desktop) - sidebar appears
xl:    1280px
2xl:   1536px
```

## 🎨 Tailwind Common Classes

```
Padding:      p-4, px-6, py-2
Margin:       m-4, mx-auto, my-2
Gap:          gap-4
Rounded:      rounded-xl, rounded-2xl
Borders:      border, border-[var(--border)]
Shadows:      shadow, shadow-lg
Text:         text-sm, font-semibold, text-center
Flex:         flex, items-center, justify-between, gap-2
Grid:         grid, grid-cols-2, md:grid-cols-3, gap-6
Display:      hidden, block, flex, grid
Width:        w-full, w-32, max-w-md
Colors:       text-[var(--text-primary)], bg-[var(--surface)]
```

## 🔄 RTL-Safe Classes

```blade
{{ session('rtl') ? 'right-3' : 'left-3' }}          <!-- Opposite sides -->
{{ session('rtl') ? 'mr-4' : 'ml-4' }}              <!-- Opposite margins -->
{{ session('rtl') ? 'text-right' : 'text-left' }}   <!-- Text alignment -->

<!-- Or use Tailwind RTL modifier -->
<div class="ml-4 rtl:ml-0 rtl:mr-4">...</div>
```

## 🌍 Session Variables

```blade
{{ session('rtl') }}     <!-- true = Arabic, false = English -->
{{ session('theme') }}   <!-- 'light' or 'dark' -->
```

## 📝 Form Example

```blade
@extends('admin::layouts.app')

@section('content')
  <h1>Create Item</h1>

  <form method="POST" action="{{ route('items.store') }}">
    @csrf

    <x-admin::ui.card>
      <x-admin::forms.form-grid :columns="2">
        <x-admin::ui.input 
          name="name"
          label="Name"
          value="{{ old('name') }}"
          required />

        <x-admin::ui.dropdown 
          name="category"
          label="Category"
          :options="$categories"
          value="{{ old('category') }}" />

        <div class="col-span-2">
          <x-admin::ui.input 
            name="description"
            label="Description"
            type="textarea"
            value="{{ old('description') }}" />
        </div>
      </x-admin::forms.form-grid>
    </x-admin::ui.card>

    <x-admin::forms.form-actions 
      submitLabel="Create"
      cancelHref="{{ route('items.index') }}" />
  </form>
@endsection
```

## 📊 Table Example

```blade
<x-admin::ui.card noPadding="true">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-[var(--surface-2)] border-b border-[var(--border)]">
        <tr>
          <th class="px-6 py-4 text-left">Name</th>
          <th class="px-6 py-4 text-left">Status</th>
          <th class="px-6 py-4 text-left">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[var(--border)]">
        @foreach ($items as $item)
          <tr class="hover:bg-[var(--surface-2)]">
            <td class="px-6 py-4">{{ $item->name }}</td>
            <td class="px-6 py-4">
              <x-admin::ui.badge variant="success">Active</x-admin::ui.badge>
            </td>
            <td class="px-6 py-4">
              <x-admin::ui.button variant="ghost" href="#">Edit</x-admin::ui.button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <x-admin::ui.pagination :paginator="$items" />
</x-admin::ui.card>
```

## ✅ Keyboard Shortcuts (for future implementation)

```
Ctrl/Cmd + K    Search
Ctrl/Cmd + /    Command palette
Escape          Close modals/dropdowns
Tab             Navigation
Enter           Confirm action
```

---

**For full documentation, see: ADMIN_UI_GUIDE.md**
