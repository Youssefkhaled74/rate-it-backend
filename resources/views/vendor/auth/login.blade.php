<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ __('vendor.login') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
            ar: ['Almarai', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
          },
          boxShadow: {
            soft: '0 20px 60px rgba(0,0,0,0.12)'
          }
        }
      },
      darkMode: 'class'
    }
  </script>
  <style>
    body { font-feature-settings: "cv02", "cv03", "cv04", "cv11"; }
    [dir="rtl"] body { font-family: 'Almarai', ui-sans-serif, system-ui, -apple-system, sans-serif; }
  </style>
</head>
<body class="min-h-screen text-gray-900 dark:text-gray-100">
  <div class="min-h-screen bg-gradient-to-br from-rose-50 via-white to-red-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute -top-24 -right-24 w-96 h-96 bg-red-200/40 dark:bg-red-500/20 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-rose-200/40 dark:bg-rose-500/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative min-h-screen flex items-center justify-center p-6">
      <div class="absolute top-6 right-6">
        <div class="flex items-center gap-3 rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur px-3 py-2 border border-white/70 dark:border-slate-800 shadow-sm">
          <div class="flex items-center gap-1 rounded-xl bg-white/80 dark:bg-slate-900/80 p-1 border border-gray-200/70 dark:border-slate-700">
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
               class="px-3 py-1.5 rounded-lg text-[11px] font-semibold {{ app()->getLocale() === 'en' ? 'bg-red-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-300' }}">
              EN
            </a>
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'ar']) }}"
               class="px-3 py-1.5 rounded-lg text-[11px] font-semibold {{ app()->getLocale() === 'ar' ? 'bg-red-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-300' }}">
              AR
            </a>
          </div>
          <button type="button" id="themeToggle"
                  class="px-3 py-1.5 rounded-xl text-[11px] font-semibold border border-gray-200/70 dark:border-slate-700 bg-white/80 dark:bg-slate-900/80 text-gray-700 dark:text-gray-200">
            <span class="theme-label">Light</span>
          </button>
          <a href="{{ url('/admin/login') }}"
             class="px-3 py-1.5 rounded-xl text-[11px] font-semibold text-white bg-gradient-to-r from-red-600 to-rose-600 shadow-sm hover:from-red-700 hover:to-rose-700">
            Admin Login
          </a>
        </div>
      </div>
      <div class="w-full max-w-4xl grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
        <div class="hidden lg:block">
          <div class="text-sm font-semibold text-red-700">Rateit</div>
          <div class="mt-2 text-3xl font-bold leading-tight">
            {{ __('vendor.panel') }}
          </div>
          <p class="mt-3 text-gray-600 dark:text-gray-300">
            {{ __('vendor.login_hint') }}
          </p>
          <div class="mt-6 space-y-3 text-sm text-gray-600 dark:text-gray-300">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 bg-red-600 rounded-full"></span>
              <span>Brand dashboard and reviews</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 bg-red-600 rounded-full"></span>
              <span>Voucher verification and history</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 bg-red-600 rounded-full"></span>
              <span>Branch staff management</span>
            </div>
          </div>
        </div>

        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur rounded-3xl p-8 shadow-soft border border-white/80 dark:border-slate-800">
          <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-red-700 text-white flex items-center justify-center font-bold">
              R
            </div>
            <div>
              <div class="text-xl font-semibold">{{ __('vendor.login') }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('vendor.login_hint') }}</div>
            </div>
          </div>

          <form method="POST" action="{{ route('vendor.login.submit') }}" class="mt-6 space-y-4">
            @csrf
            <div>
              <label class="block text-sm font-medium mb-1">{{ __('vendor.phone') }}</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">☎</span>
                <input name="phone" value="{{ old('phone') }}" class="w-full border dark:border-slate-700 dark:bg-slate-900 rounded-xl px-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-200" placeholder="05xxxxxxxx" />
              </div>
              @error('phone')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">{{ __('vendor.password') }}</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">🔒</span>
                <input type="password" name="password" class="w-full border dark:border-slate-700 dark:bg-slate-900 rounded-xl px-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-200" placeholder="••••••••" />
              </div>
              @error('password')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="w-full bg-red-700 text-white rounded-xl py-2.5 font-semibold hover:bg-red-800 transition">
              {{ __('vendor.login') }}
            </button>
          </form>

          <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
            {{ __('vendor.forbidden') }}: Only vendor users can access this area.
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    (function() {
      const root = document.documentElement;
      const toggle = document.getElementById('themeToggle');
      const label = toggle?.querySelector('.theme-label');
      const saved = localStorage.getItem('vendor_theme');
      const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      const initial = saved || (prefersDark ? 'dark' : 'light');
      if (initial === 'dark') root.classList.add('dark');
      if (label) label.textContent = root.classList.contains('dark') ? 'Dark' : 'Light';
      toggle?.addEventListener('click', function() {
        root.classList.toggle('dark');
        const mode = root.classList.contains('dark') ? 'dark' : 'light';
        localStorage.setItem('vendor_theme', mode);
        if (label) label.textContent = mode === 'dark' ? 'Dark' : 'Light';
      });
    })();
  </script>
</body>
</html>

