/**
 * Configuration guide for integrating the Admin UI Kit
 * 
 * Follow these steps to properly set up the admin dashboard
 */

// =============================================================================
// 1. VITE CONFIGURATION (vite.config.js)
// =============================================================================
// Ensure these are imported in your Vite config:

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/css/admin-theme.css',    // Add this
        'resources/js/app.js',
        'resources/js/admin-ui.js',          // Add this
      ],
      refresh: true,
    }),
  ],
});

// =============================================================================
// 2. TAILWIND CONFIGURATION (tailwind.config.js)
// =============================================================================
// Ensure Tailwind is configured to support CSS variables:

export default {
  content: [
    './resources/views/**/*.blade.php',
    './Modules/*/resources/views/**/*.blade.php',  // Add this
  ],
  theme: {
    extend: {
      colors: {
        // Use CSS variables instead of hardcoded colors
        // This allows theme switching to work automatically
      },
      borderRadius: {
        // Radii are defined in admin-theme.css
      },
    },
  },
  plugins: [],
  // Important: Do NOT set theme switching in Tailwind config
  // Use data-theme attribute and CSS variables instead
};

// =============================================================================
// 3. BLADE TEMPLATE SETUP (example page)
// =============================================================================

@extends('admin::layouts.app')

@section('title', 'Page Title')

@section('content')
    <!-- Your page content here -->
@endsection

// Or with custom CSS/JS:

@extends('admin::layouts.app')

@section('title', 'Page Title')

@section('css')
    <style>
        /* Custom page styles */
    </style>
@endsection

@section('content')
    <!-- Your page content -->
@endsection

@section('js')
    <script>
        // Custom page scripts
    </script>
@endsection

// =============================================================================
// 4. COMPONENT REGISTRATION (AppServiceProvider.php)
// =============================================================================

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register Blade component namespace
        $this->loadViewsFrom(
            base_path('Modules/Admin/resources/views'),
            'admin'
        );

        // Register components from the Admin module
        $this->publishes([
            base_path('Modules/Admin/resources/views/components') => resource_path('views/components/admin'),
        ], 'admin-components');
    }
}

// =============================================================================
// 5. ROUTING SETUP (routes/web.php)
// =============================================================================

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin::pages.dashboard.index');
    })->name('admin.dashboard');

    Route::resource('categories', CategoryController::class)
        ->names('admin.categories')
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});

// Or use module-specific routing
Route::group(['namespace' => 'Modules\Admin\Http\Controllers'], function () {
    Route::middleware('auth')->prefix('admin')->group(function () {
        // Admin routes here
    });
});

// =============================================================================
// 6. MIDDLEWARE & SESSION (For RTL/LTR support)
// =============================================================================

// Create app/Http/Middleware/SetAdminPreferences.php

namespace App\Http\Middleware;

class SetAdminPreferences
{
    public function handle($request, $next)
    {
        // Set direction based on user language preference or query param
        if ($request->query('dir')) {
            session(['rtl' => $request->query('dir') === 'rtl']);
        }

        // Set theme based on query param
        if ($request->query('theme')) {
            session(['theme' => $request->query('theme')]);
        }

        return $next($request);
    }
}

// Register in app/Http/Kernel.php:
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \App\Http\Middleware\SetAdminPreferences::class,
    ],
];

// =============================================================================
// 7. USING THE COMPONENTS IN YOUR VIEWS
// =============================================================================

<!-- Example: Dashboard with stats -->
@extends('admin::layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <h1>Dashboard</h1>
        <x-admin::ui.button href="#">Create New</x-admin::ui.button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <x-admin::ui.stat-card 
            title="Total Users"
            value="{{ $totalUsers }}"
            :trend="['value' => 12, 'positive' => true]" />
    </div>

    <!-- Data Table -->
    <x-admin::ui.card>
        <table class="w-full">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <x-admin::ui.badge variant="success">Active</x-admin::ui.badge>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin::ui.card>
@endsection

// =============================================================================
// 8. THEME PERSISTENCE (Client-side)
// =============================================================================

// Theme and direction are automatically persisted in localStorage
// via the inline scripts in layouts/app.blade.php

// To manually toggle theme:
document.documentElement.setAttribute('data-theme', 'dark');
localStorage.setItem('admin_theme', 'dark');

// To manually toggle direction:
document.documentElement.setAttribute('dir', 'rtl');
localStorage.setItem('admin_direction', 'rtl');

// =============================================================================
// 9. ALPINE.JS INTEGRATION
// =============================================================================

// Alpine.js is included via CDN in layouts/app.blade.php
// Scripts are automatically initialized

// Example: Using Alpine data attributes
<div x-data="{ count: 0 }">
    <button @click="count++">Increment</button>
    <p x-text="count"></p>
</div>

// Access global utilities:
AdminUI.toast('Success message', 'success');
AdminUI.copyToClipboard('Text to copy');
AdminUI.isDarkMode(); // Returns boolean
AdminUI.isRTL();      // Returns boolean

// =============================================================================
// 10. FORM VALIDATION & ERROR HANDLING
// =============================================================================

<!-- Input with error display -->
<x-admin::ui.input 
    name="email"
    label="Email"
    value="{{ old('email') }}"
    error="{{ $errors->first('email') }}" />

<!-- Flash messages (automatic) -->
{{ redirect()->back()->with('success', 'Item saved!'); }}

<!-- Display in template -->
@include('admin::partials.flash-messages')

// =============================================================================
// 11. ACCESSIBILITY CHECKLIST
// =============================================================================

// Ensure:
// ✓ Keyboard navigation works (Tab, Enter, Escape)
// ✓ Focus indicators are visible (ring-2 ring-brand)
// ✓ ARIA labels on interactive elements
// ✓ Color contrast meets WCAG AA (4.5:1)
// ✓ Form fields have associated labels
// ✓ Semantic HTML (button, a, form, etc.)

// =============================================================================
// 12. PERFORMANCE OPTIMIZATION
// =============================================================================

// - Lazy load images with x-data="lazyImage"
// - Use Alpine.js for lightweight interactions (no heavy JS libs)
// - Minimize custom CSS (use Tailwind utilities)
// - Cache busting with Vite
// - Minify JavaScript/CSS in production

// Example: Lazy image
<img x-data="lazyImage" 
     @load="loaded = true"
     :src="loaded ? $el.dataset.src : ''"
     data-src="/path/to/image.jpg"
     class="w-full" />

// =============================================================================
// 13. CUSTOMIZATION EXAMPLES
// =============================================================================

// Change brand color in resources/css/admin-theme.css:
:root {
  --brand: #your-color;
  --brand-light: #your-light-variant;
  --brand-dark: #your-dark-variant;
}

// Add custom component in components/ui/your-component.blade.php
@props(['label' => ''])
<div class="...">
  {{ $slot }}
</div>

// Use in views:
<x-admin::ui.your-component label="Text">Content</x-admin::ui.your-component>

// =============================================================================
// 14. TROUBLESHOOTING
// =============================================================================

// Q: Theme toggle not persisting?
// A: Check browser localStorage is enabled
//    Verify admin-theme.css is imported in layouts/app.blade.php

// Q: RTL not working?
// A: Ensure SetAdminPreferences middleware is registered
//    Check dir attribute is set on <html> element

// Q: Components not rendering?
// A: Verify namespace in app/Providers/AppServiceProvider.php
//    Check blade.php config has correct view path

// Q: Alpine.js not initializing?
// A: Ensure Alpine.js CDN is loaded before custom scripts
//    Check Alpine.data() calls are in alpine:init event

// =============================================================================
