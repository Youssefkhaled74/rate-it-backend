# Rate It Admin Dashboard - UI Kit Documentation

A premium, modern Laravel Blade admin dashboard matching the mobile app's visual language. Built with Tailwind CSS, Alpine.js, and a modular architecture.

## 🎨 Design System

### Color Palette

**Light Mode (Default)**
- Background: `#ffffff`
- Surface: `#ffffff` with accents at `#f5f5f5`
- Text Primary: `#1f2937`
- Text Secondary: `#6b7280`
- Brand Red: `#dc2626`
- Success: `#10b981`
- Warning: `#f59e0b`
- Danger: `#ef4444`
- Info: `#3b82f6`

**Dark Mode**
- Background: `#0f172a`
- Surface: `#1e293b` with accents at `#334155`
- Text Primary: `#f1f5f9`
- Text Secondary: `#cbd5e1`
- Brand Red: `#ef4444` (lighter for visibility)

### CSS Variables

All colors are defined as CSS variables in `resources/css/admin-theme.css`:

```css
:root {
  --bg: #ffffff;
  --surface: #ffffff;
  --text-primary: #1f2937;
  --brand: #dc2626;
  --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

[data-theme="dark"] {
  --bg: #0f172a;
  --surface: #1e293b;
  --text-primary: #f1f5f9;
  --brand: #ef4444;
}
```

**Usage in Blade/Tailwind:**
```html
<div class="bg-[var(--surface)] text-[var(--text-primary)]">
  Content with theme colors
</div>
```

### Spacing Scale

- **4px**: `$spacing[1]`
- **8px**: `$spacing[2]`
- **12px**: `$spacing[3]`
- **16px**: `$spacing[4]` (recommended for paddings)
- **24px**: `$spacing[6]`
- **32px**: `$spacing[8]` (section margins)

### Border Radius

- Small: `0.375rem` (6px) - inputs, badges
- Medium: `0.75rem` (12px) - dropdown, modals
- Large: `1rem` (16px) - cards
- **Extra Large: `1.5rem` (24px) - primary buttons (brand-defining)**
- 2XL: `2rem` (32px) - prominent card radius

### Typography

- **Page Title (h1)**: `3xl` (30px) font-bold, line-height: tight
- **Section Title (h2)**: `2xl` (24px) font-bold
- **Card Title (h3)**: `lg` (18px) font-semibold
- **Body**: `base` (16px) with relaxed line-height
- **Small**: `sm` (14px) for secondary info
- **Micro**: `xs` (12px) for labels & helpers

### Shadows

- **sm**: Subtle hover effects
- **default**: Card shadows
- **md**: Hover card elevation
- **lg**: Modal shadows
- Use `shadow-[var(--shadow)]` for consistency

---

## 📁 Project Structure

```
Modules/Admin/resources/views/
├── layouts/
│   ├── app.blade.php          # Main authenticated layout
│   └── auth.blade.php          # Login/auth layout
├── partials/
│   ├── sidebar.blade.php       # Collapsible navigation
│   ├── topbar.blade.php        # Header with search & theme
│   ├── breadcrumbs.blade.php   # Dynamic breadcrumb navigation
│   └── flash-messages.blade.php # Toast/alert messages
├── components/
│   ├── ui/
│   │   ├── card.blade.php
│   │   ├── stat-card.blade.php
│   │   ├── button.blade.php
│   │   ├── input.blade.php
│   │   ├── dropdown.blade.php
│   │   ├── badge.blade.php
│   │   ├── modal.blade.php
│   │   ├── table.blade.php
│   │   ├── pagination.blade.php
│   │   ├── toast.blade.php
│   │   ├── empty-state.blade.php
│   │   ├── skeleton.blade.php
│   │   └── confirm-delete.blade.php
│   └── forms/
│       ├── filter-bar.blade.php
│       ├── form-grid.blade.php
│       └── form-actions.blade.php
└── pages/
    ├── dashboard/
    │   └── index.blade.php
    ├── categories/
    │   ├── index.blade.php
    │   └── create.blade.php
    └── [other modules]
```

---

## 🎛️ Theme & Direction Toggle

### Theme Persistence

Themes are persisted in localStorage and applied before page render to prevent FOUC (Flash of Unstyled Content):

```html
<script>
  (function() {
    const theme = localStorage.getItem('admin_theme') || 'light';
    const direction = localStorage.getItem('admin_direction') || 'ltr';
    document.documentElement.setAttribute('data-theme', theme);
    document.documentElement.setAttribute('dir', direction);
  })();
</script>
```

### Theme Toggle (Topbar)

```blade
@click="
  document.documentElement.setAttribute(
    'data-theme', 
    document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'
  );
  localStorage.setItem('admin_theme', document.documentElement.getAttribute('data-theme'));
"
```

### RTL/LTR Toggle

```blade
@click="
  const newDir = document.documentElement.getAttribute('dir') === 'rtl' ? 'ltr' : 'rtl';
  document.documentElement.setAttribute('dir', newDir);
  document.documentElement.lang = newDir === 'rtl' ? 'ar' : 'en';
  localStorage.setItem('admin_direction', newDir);
"
```

### Conditional Classes (RTL Example)

```blade
<!-- Left in LTR, Right in RTL -->
{{ session('rtl') ? 'right-3' : 'left-3' }}

<!-- Padding adjustment -->
$icon && $iconPosition === 'left' ? (session('rtl') ? 'pr-10' : 'pl-10') : ''
```

---

## 🧩 Component Usage Guide

### Card Component

```blade
<x-admin::ui.card hoverable="true" border="true">
  <h3>Card Title</h3>
  <p>Card content here</p>
</x-admin::ui.card>
```

**Props:**
- `hoverable` (bool): Enable hover shadow effect
- `border` (bool): Show border (default: true)
- `clickable` (bool): Add cursor pointer
- `noPadding` (bool): Remove default padding

### Stat Card Component

```blade
<x-admin::ui.stat-card 
  title="Total Users"
  value="12,847"
  subtitle="active users"
  :trend="['value' => 12, 'positive' => true]"
  icon="<svg>...</svg>" />
```

### Button Component

```blade
<x-admin::ui.button 
  variant="primary"
  size="md"
  href="/link"
  icon="<svg>...</svg>"
  iconPosition="left"
  loading="false"
  disabled="false"
  fullWidth="false">
  Button Text
</x-admin::ui.button>
```

**Variants:** `primary`, `secondary`, `ghost`, `danger`  
**Sizes:** `sm`, `md`, `lg`

### Input Component

```blade
<x-admin::ui.input 
  name="email"
  label="Email Address"
  type="email"
  placeholder="user@example.com"
  value="{{ old('email') }}"
  required="true"
  icon="<svg>...</svg>"
  iconPosition="left"
  helpText="Enter your email address"
  error="Custom error message" />
```

### Dropdown/Select Component

```blade
<x-admin::ui.dropdown 
  name="category"
  label="Select Category"
  :options="['1' => 'Option 1', '2' => 'Option 2']"
  value="{{ old('category') }}"
  placeholder="Choose an option"
  multiple="false"
  required="true" />
```

### Badge Component

```blade
<x-admin::ui.badge variant="success" size="md">
  {{ $status }}
</x-admin::ui.badge>
```

**Variants:** `success`, `warning`, `danger`, `info`, `neutral`  
**Sizes:** `sm`, `md`, `lg`

### Modal Component

```blade
<x-admin::ui.modal id="modal-example" title="Modal Title" maxWidth="md">
  <p>Modal content goes here</p>
  
  <div class="flex gap-3 justify-end mt-6">
    <button @click="$dispatch('modal-example-close')">Cancel</button>
    <button class="bg-[var(--brand)] text-white px-4 py-2 rounded-xl">Save</button>
  </div>
</x-admin::ui.modal>

<!-- Trigger modal -->
<button @click="$dispatch('modal-example-open')">Open Modal</button>
```

### Table Component

```blade
<x-admin::ui.table :headers="['Name', 'Email', 'Status']">
  @foreach ($users as $user)
    <tr>
      <td class="px-6 py-4">{{ $user->name }}</td>
      <td class="px-6 py-4">{{ $user->email }}</td>
      <td class="px-6 py-4">
        <x-admin::ui.badge variant="success">Active</x-admin::ui.badge>
      </td>
    </tr>
  @endforeach
</x-admin::ui.table>
```

### Confirm Delete Modal

```blade
<x-admin::ui.confirm-delete 
  id="delete-item"
  title="Delete Item"
  message="Are you sure?"
  itemName="{{ $item->name }}"
  action="{{ route('items.destroy', $item) }}"
  method="DELETE" />

<!-- Trigger -->
<button @click="$dispatch('delete-item-open')">Delete</button>
```

### Empty State Component

```blade
<x-admin::ui.empty-state 
  title="No items found"
  description="Start by creating a new item"
  actionLabel="Create New"
  actionHref="{{ route('items.create') }}" 
  icon="<svg>...</svg>" />
```

### Skeleton Loader

```blade
<!-- Row skeleton -->
<x-admin::ui.skeleton type="row" count="5" />

<!-- Card skeleton -->
<x-admin::ui.skeleton type="card" count="3" />

<!-- Line skeleton -->
<x-admin::ui.skeleton type="line" count="4" />
```

### Toast Component

```blade
<x-admin::ui.toast 
  id="success-toast"
  variant="success"
  message="Item saved successfully"
  duration="5000" />
```

**Variants:** `success`, `error`, `warning`, `info`

### Filter Bar Component

```blade
<form method="GET">
  <x-admin::forms.filter-bar>
    <!-- Advanced filters added here -->
    <x-admin::ui.dropdown 
      name="date_range"
      label="Date Range"
      :options="['today' => 'Today', 'week' => 'This Week']" />
  </x-admin::forms.filter-bar>
</form>
```

### Form Grid Component

```blade
<x-admin::forms.form-grid :columns="2">
  <x-admin::ui.input name="first_name" label="First Name" />
  <x-admin::ui.input name="last_name" label="Last Name" />
</x-admin::forms.form-grid>
```

**Columns:** `1`, `2` (default), `3`

### Form Actions (Sticky Footer)

```blade
<form method="POST">
  @csrf
  
  <!-- Form content -->
  
  <x-admin::forms.form-actions 
    submitLabel="Save Changes"
    cancelHref="{{ route('items.index') }}" />
</form>
```

---

## 🎯 Alpine.js Integration

All interactive features use Alpine.js. Key patterns:

### Sidebar Toggle (Mobile)

```blade
<div x-data="{ sidebarOpen: false }">
  <button @click="sidebarOpen = !sidebarOpen">Menu</button>
  
  <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <!-- Sidebar content -->
  </aside>
</div>
```

### Dropdown Menu

```blade
<div x-data="{ open: false }" @click.away="open = false">
  <button @click="open = !open">Toggle</button>
  
  <div x-show="open" x-transition class="...">
    <!-- Dropdown items -->
  </div>
</div>
```

### Modal Behavior

```blade
<div x-data="{ open: false }" @modal-id-open.window="open = true" @modal-id-close.window="open = false">
  <div x-show="open" @click.self="open = false">
    <!-- Modal content -->
  </div>
</div>

<button @click="$dispatch('modal-id-open')">Open</button>
```

---

## 🌍 RTL/LTR Strategy

### Implementation

1. **Session-based language**: `session('rtl')` returns true for Arabic
2. **HTML attribute**: `<html dir="{{ session('rtl') ? 'rtl' : 'ltr' }}">`
3. **CSS auto-handling**: Tailwind respects `dir="rtl"` automatically
4. **JavaScript toggle**: Updates both DOM and localStorage

### Examples

```blade
<!-- Conditional spacing -->
{{ session('rtl') ? 'mr-4' : 'ml-4' }}

<!-- Icon mirroring -->
<svg :class="session('rtl') ? 'scale-x-[-1]' : ''">...</svg>

<!-- Text alignment -->
{{ session('rtl') ? 'text-right' : 'text-left' }}
```

### RTL-Safe Tailwind Classes

Tailwind's `rtl:` modifier:

```html
<div class="ml-4 rtl:mr-4 rtl:ml-0">
  Works in both directions automatically
</div>
```

---

## 📊 Accessibility

### Keyboard Navigation

- **Tab**: Navigate between interactive elements
- **Enter**: Activate buttons, open modals
- **Escape**: Close modals, dropdowns
- **Arrow Keys**: Navigate dropdown options (custom implementation needed)

### ARIA Labels

```blade
<button aria-label="Open notifications menu">
  <svg>...</svg>
</button>

<input aria-describedby="email-help" />
<span id="email-help">We'll never share your email</span>
```

### Focus Management

All components include focus rings:

```css
.focus-ring {
  outline: none;
  ring: 2px var(--brand);
  ring-offset: 2px;
}
```

Used automatically on buttons, inputs, and interactive elements.

### Color Contrast

- Text on background: 4.5:1 ratio (WCAG AA)
- UI indicators: Multiple cues (color + icon/text)
- No information conveyed by color alone

---

## 🚀 Usage Examples

### Dashboard Page

```blade
@extends('admin::layouts.app')

@section('content')
  <!-- Stats -->
  <div class="grid grid-cols-4 gap-6 mb-8">
    <x-admin::ui.stat-card title="Users" value="1,234" />
  </div>

  <!-- Table -->
  <x-admin::ui.card>
    <table class="w-full">...</table>
  </x-admin::ui.card>
@endsection
```

### CRUD Index Page

```blade
@extends('admin::layouts.app')

@section('content')
  <!-- Header + Create Button -->
  <div class="flex justify-between items-center mb-8">
    <h1>Items</h1>
    <x-admin::ui.button href="{{ route('items.create') }}">New Item</x-admin::ui.button>
  </div>

  <!-- Filters -->
  <x-admin::forms.filter-bar />

  <!-- Table with Actions -->
  <x-admin::ui.card>
    <table>
      <thead>...</thead>
      <tbody>
        @foreach ($items as $item)
          <tr>
            <td>{{ $item->name }}</td>
            <td>
              <x-admin::ui.button href="{{ route('items.edit', $item) }}">Edit</x-admin::ui.button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </x-admin::ui.card>

  <!-- Pagination -->
  <x-admin::ui.pagination :paginator="$items" />
@endsection
```

### CRUD Create/Edit Page

```blade
@extends('admin::layouts.app')

@section('content')
  <form method="POST" action="{{ route('items.store') }}">
    @csrf

    <x-admin::forms.form-grid :columns="2">
      <x-admin::ui.input name="name" label="Name" value="{{ old('name') }}" />
      <x-admin::ui.input name="email" label="Email" type="email" />
    </x-admin::forms.form-grid>

    <x-admin::forms.form-actions submitLabel="Save" cancelHref="{{ route('items.index') }}" />
  </form>
@endsection
```

---

## 🔧 Customization

### Change Brand Color

Update `resources/css/admin-theme.css`:

```css
:root {
  --brand: #your-color;
  --brand-light: #lighter-variant;
  --brand-dark: #darker-variant;
}
```

### Add New Component

1. Create file: `Modules/Admin/resources/views/components/ui/your-component.blade.php`
2. Use slot pattern:

```blade
@props(['label' => ''])

<div class="...">
  {{ $slot }}
</div>
```

3. Use in views:

```blade
<x-admin::ui.your-component label="Text">
  Content
</x-admin::ui.your-component>
```

### Override Component

Create at higher priority:

```
resources/views/components/admin/ui/button.blade.php
```

---

## 📝 Notes

- **No external icons library**: Use inline SVGs or sprite sheets
- **Minimal dependencies**: Only Alpine.js (no jQuery, no Axios)
- **Production-ready**: All states handled (loading, error, disabled)
- **Performance**: Optimized for fast page loads and smooth interactions
- **Mobile-first**: Responsive design with Tailwind breakpoints

---

## 📚 Reference Files

- Design tokens: `resources/css/admin-theme.css`
- Main layout: `Modules/Admin/resources/views/layouts/app.blade.php`
- Components: `Modules/Admin/resources/views/components/`
- Example pages: `Modules/Admin/resources/views/pages/`

---

**Last Updated:** January 2026  
**Version:** 1.0.0 (Production Ready)
