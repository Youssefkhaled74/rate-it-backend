{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0b0b">

    <title>{{ config('app.name', 'RateIt') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        {{-- Fallback minimal CSS (in case Vite build isn't available) --}}
        <style>
            :root{
                --bg:#0a0a0a; --card:#101010; --muted:#a3a3a3; --text:#f5f5f5;
                --line:rgba(255,255,255,.08); --accent:#b91c1c; --accent2:#f59e0b;
            }
            *{box-sizing:border-box} html,body{height:100%}
            body{margin:0;font-family:"Instrument Sans",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:radial-gradient(1200px 600px at 20% 10%, rgba(185,28,28,.25), transparent 60%),
                radial-gradient(900px 500px at 80% 20%, rgba(245,158,11,.18), transparent 60%),
                linear-gradient(180deg, #070707, #0b0b0b 40%, #070707);
                color:var(--text)}
            a{color:inherit;text-decoration:none}
            .container{max-width:1100px;margin:0 auto;padding:24px}
            .nav{display:flex;align-items:center;justify-content:space-between;gap:12px}
            .brand{display:flex;align-items:center;gap:10px}
            .logo{width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,var(--accent),var(--accent2));display:grid;place-items:center;box-shadow:0 10px 30px rgba(0,0,0,.35)}
            .pill{display:inline-flex;gap:8px;align-items:center;padding:8px 12px;border:1px solid var(--line);border-radius:999px;background:rgba(16,16,16,.6);backdrop-filter: blur(10px)}
            .btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;padding:12px 16px;border-radius:14px;border:1px solid var(--line);background:rgba(16,16,16,.55);cursor:pointer}
            .btnPrimary{background:linear-gradient(135deg,var(--accent),#ef4444);border-color:rgba(255,255,255,.10)}
            .btn:hover{transform:translateY(-1px)} .btn{transition:.15s ease}
            .hero{padding:40px 0 18px;display:grid;grid-template-columns:1.2fr .8fr;gap:18px;align-items:stretch}
            .card{border:1px solid var(--line);border-radius:22px;background:rgba(16,16,16,.55);backdrop-filter: blur(14px);box-shadow:0 20px 70px rgba(0,0,0,.45)}
            .cardPad{padding:22px}
            .h1{font-size:42px;line-height:1.05;margin:14px 0 10px}
            .p{color:var(--muted);margin:0;line-height:1.6}
            .grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:14px}
            .mini{padding:14px;border-radius:18px;border:1px solid var(--line);background:rgba(0,0,0,.12)}
            .mini b{display:block;margin-bottom:6px}
            .footer{padding:18px 0 30px;color:rgba(255,255,255,.6);font-size:13px;display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap}
            .sep{height:1px;background:var(--line);margin:16px 0}
            @media (max-width: 960px){ .hero{grid-template-columns:1fr} .h1{font-size:36px} .grid{grid-template-columns:1fr} }
        </style>
    @endif
</head>

<body class="min-h-screen bg-neutral-950 text-neutral-50 antialiased">
    {{-- Background ornaments (Tailwind-first; safe even with fallback) --}}
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -top-24 -left-24 h-[420px] w-[420px] rounded-full bg-red-600/20 blur-3xl"></div>
        <div class="absolute -top-20 -right-24 h-[380px] w-[380px] rounded-full bg-amber-400/15 blur-3xl"></div>
        <div class="absolute bottom-[-160px] left-1/2 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-red-600/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-6xl px-6 py-6 lg:px-8">
        {{-- Top Nav --}}
        <header class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-2xl bg-gradient-to-br from-red-700 to-amber-400 shadow-2xl shadow-black/40 flex items-center justify-center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 2l2.4 6.7 7.1.1-5.7 4.1 2.2 6.8L12 15.9 6 19.7l2.2-6.8L2.5 8.8l7.1-.1L12 2z" stroke="currentColor" stroke-width="1.6" class="text-white"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-neutral-400">Welcome to</div>
                    <div class="text-lg font-semibold tracking-tight">
                        {{ config('app.name', 'RateIt') }}
                    </div>
                </div>
            </div>

            @if (Route::has('login'))
                <nav class="flex items-center gap-2">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium hover:bg-white/10 transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium hover:bg-white/10 transition">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-br from-red-700 to-red-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-red-900/30 hover:opacity-95 transition">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        {{-- Hero --}}
        <main class="mt-10 grid gap-6 lg:grid-cols-12">
            {{-- Left: Main pitch --}}
            <section class="lg:col-span-7">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/40 backdrop-blur-xl lg:p-10">
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-black/20 px-4 py-2 text-xs font-medium text-neutral-300">
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                        Laravel 12 • Clean Architecture • Unified API Responses
                    </div>

                    <h1 class="mt-5 text-4xl font-semibold tracking-tight text-white lg:text-5xl">
                        Rate the world. Earn rewards. <span class="text-neutral-300">Build trust.</span>
                    </h1>

                    <p class="mt-4 text-base leading-relaxed text-neutral-300">
                        RateIt is a multi-platform rating ecosystem (User / Admin / Vendor) designed for authentic reviews,
                        onboarding experiences, and secure authentication with a clean, scalable backend structure.
                    </p>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a href="{{ url('/api') }}"
                           class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-br from-red-700 to-red-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/30 hover:opacity-95 transition">
                            Explore APIs
                            <svg class="ml-2 h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 17L17 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M9 7h8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </a>

                        <a href="{{ url('/') }}"
                           class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-5 py-3 text-sm font-medium text-white hover:bg-white/10 transition">
                            View Status
                        </a>

                        <div class="text-xs text-neutral-400">
                            Tip: use Postman collection to test the User endpoints.
                        </div>
                    </div>

                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-black/10 p-4">
                            <div class="text-sm font-semibold">User App</div>
                            <div class="mt-1 text-sm text-neutral-300">Onboarding, auth, reviews, rewards.</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-black/10 p-4">
                            <div class="text-sm font-semibold">Vendor</div>
                            <div class="mt-1 text-sm text-neutral-300">Branches, QR flow, campaigns, stats.</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-black/10 p-4">
                            <div class="text-sm font-semibold">Admin</div>
                            <div class="mt-1 text-sm text-neutral-300">Moderation, dashboards, control center.</div>
                        </div>
                    </div>

                    <div class="mt-8 h-px w-full bg-white/10"></div>

                    <div class="mt-6 flex flex-wrap items-center gap-3 text-sm text-neutral-300">
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5">
                            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                            Multilingual responses (AR/EN)
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5">
                            <span class="h-2 w-2 rounded-full bg-sky-400"></span>
                            Resources + Service layer
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5">
                            <span class="h-2 w-2 rounded-full bg-fuchsia-400"></span>
                            Sanctum auth
                        </span>
                    </div>
                </div>
            </section>

            {{-- Right: Quickstart / Docs card --}}
            <aside class="lg:col-span-5">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/40 backdrop-blur-xl lg:p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-neutral-400">Quickstart</div>
                            <div class="text-lg font-semibold">Get productive fast</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-black/20 px-3 py-2 text-xs text-neutral-300">
                            v1 APIs
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        <a href="https://laravel.com/docs" target="_blank"
                           class="group block rounded-2xl border border-white/10 bg-black/10 p-4 hover:bg-white/5 transition">
                            <div class="flex items-center justify-between">
                                <div class="font-medium">Laravel Documentation</div>
                                <span class="text-neutral-400 group-hover:text-white transition">↗</span>
                            </div>
                            <div class="mt-1 text-sm text-neutral-300">
                                Learn routing, validation, resources, and middleware.
                            </div>
                        </a>

                        <a href="https://laracasts.com" target="_blank"
                           class="group block rounded-2xl border border-white/10 bg-black/10 p-4 hover:bg-white/5 transition">
                            <div class="flex items-center justify-between">
                                <div class="font-medium">Laracasts</div>
                                <span class="text-neutral-400 group-hover:text-white transition">↗</span>
                            </div>
                            <div class="mt-1 text-sm text-neutral-300">
                                Best practical tutorials to speed up development.
                            </div>
                        </a>

                        <div class="rounded-2xl border border-white/10 bg-black/10 p-4">
                            <div class="font-medium">Suggested flow</div>
                            <ol class="mt-2 space-y-2 text-sm text-neutral-300">
                                <li class="flex gap-2">
                                    <span class="mt-1 inline-block h-2 w-2 rounded-full bg-red-500"></span>
                                    Run migrations + seeders (onboarding screens, etc.)
                                </li>
                                <li class="flex gap-2">
                                    <span class="mt-1 inline-block h-2 w-2 rounded-full bg-amber-400"></span>
                                    Import Postman collection & environment
                                </li>
                                <li class="flex gap-2">
                                    <span class="mt-1 inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                                    Test Auth endpoints (register/login/me/logout)
                                </li>
                            </ol>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ url('/dashboard') }}"
                           class="inline-flex w-full items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-medium hover:bg-white/10 transition">
                            Open Dashboard
                        </a>
                        <a href="{{ url('/') }}"
                           class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-red-600 px-4 py-3 text-sm font-semibold text-black hover:opacity-95 transition">
                            Deploy Ready
                        </a>
                    </div>

                    <p class="mt-5 text-xs text-neutral-400">
                        This is a landing page only. Your mobile app will consume the API endpoints (User/Admin/Vendor modules).
                    </p>
                </div>
            </aside>
        </main>

        {{-- Footer --}}
        <footer class="mt-10 flex flex-col gap-3 border-t border-white/10 pt-6 text-sm text-neutral-400 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                <span>Backend running</span>
                <span class="text-neutral-600">•</span>
                <span>{{ config('app.env') }}</span>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <span>&copy; {{ date('Y') }} {{ config('app.name', 'RateIt') }}</span>
                <span class="text-neutral-600">•</span>
                <span>Built with Laravel</span>
            </div>
        </footer>
    </div>
</body>
</html>
