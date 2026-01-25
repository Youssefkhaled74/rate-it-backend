{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0b0b">

    <title>{{ config('app.name', 'RateIt') }}</title>
    <meta name="description" content="RateIt — multi-platform rating ecosystem (User/Admin/Vendor) with clean architecture and unified API responses.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        {{-- Fallback minimal CSS (polished, responsive, accessible) --}}
        <style>
            :root{
                --bg0:#070707; --bg1:#0b0b0b; --card:rgba(255,255,255,.04);
                --line:rgba(255,255,255,.09); --text:#f5f5f5; --muted:rgba(255,255,255,.68);
                --muted2:rgba(255,255,255,.52);
                --red:#ef4444; --red2:#b91c1c; --amber:#f59e0b; --green:#34d399; --blue:#60a5fa;
                --r:22px;
            }
            *{box-sizing:border-box}
            html,body{height:100%}
            body{
                margin:0;
                font-family:"Instrument Sans",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
                color:var(--text);
                background:
                    radial-gradient(1200px 600px at 20% 10%, rgba(239,68,68,.22), transparent 60%),
                    radial-gradient(900px 500px at 80% 15%, rgba(245,158,11,.16), transparent 60%),
                    radial-gradient(900px 650px at 50% 120%, rgba(96,165,250,.10), transparent 60%),
                    linear-gradient(180deg, var(--bg0), var(--bg1) 40%, var(--bg0));
            }
            a{color:inherit;text-decoration:none}
            .wrap{max-width:1120px;margin:0 auto;padding:24px}
            .skip{position:absolute;left:-999px;top:12px;background:#fff;color:#000;padding:10px 12px;border-radius:12px}
            .skip:focus{left:12px;z-index:50}
            .nav{display:flex;align-items:center;justify-content:space-between;gap:12px}
            .brand{display:flex;align-items:center;gap:12px}
            .logo{
                width:44px;height:44px;border-radius:16px;
                background:linear-gradient(135deg,var(--red2),var(--amber));
                box-shadow:0 18px 60px rgba(0,0,0,.55), 0 0 0 1px rgba(255,255,255,.08) inset;
                display:grid;place-items:center;
            }
            .kicker{font-size:12px;color:var(--muted2);margin-bottom:2px}
            .name{font-size:16px;font-weight:700;letter-spacing:-.02em}
            .navlinks{display:flex;align-items:center;gap:10px}
            .btn{
                display:inline-flex;align-items:center;justify-content:center;gap:10px;
                padding:11px 14px;border-radius:16px;
                border:1px solid var(--line); background:rgba(255,255,255,.04);
                transition:transform .15s ease, background .15s ease, border-color .15s ease;
                outline:none;
            }
            .btn:focus{box-shadow:0 0 0 3px rgba(96,165,250,.35)}
            .btn:hover{transform:translateY(-1px);background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.14)}
            .btnPrimary{
                border-color:rgba(255,255,255,.10);
                background:linear-gradient(135deg,var(--red2),var(--red));
                box-shadow:0 18px 45px rgba(185,28,28,.22);
                font-weight:700;
            }
            .badge{
                display:inline-flex;align-items:center;gap:8px;
                padding:8px 12px;border-radius:999px;
                border:1px solid var(--line); background:rgba(0,0,0,.18);
                color:var(--muted); font-size:12px; font-weight:600;
            }
            .dot{width:8px;height:8px;border-radius:999px;background:var(--green);box-shadow:0 0 0 6px rgba(52,211,153,.12)}
            .grid{display:grid;gap:14px;margin-top:18px}
            .main{
                display:grid;grid-template-columns:1.15fr .85fr;gap:16px;margin-top:18px;align-items:stretch;
            }
            .card{
                border:1px solid var(--line); border-radius:calc(var(--r) + 6px);
                background:var(--card);
                box-shadow:0 24px 90px rgba(0,0,0,.55);
                backdrop-filter: blur(14px);
            }
            .pad{padding:22px}
            .heroTitle{
                font-size:46px;line-height:1.03;margin:12px 0 10px;
                letter-spacing:-.03em;
            }
            .grad{
                background:linear-gradient(90deg,#fff,rgba(255,255,255,.78));
                -webkit-background-clip:text;background-clip:text;color:transparent;
            }
            .sub{color:var(--muted);line-height:1.7;margin:0}
            .cta{display:flex;flex-wrap:wrap;gap:10px;margin-top:18px;align-items:center}
            .hint{font-size:12px;color:var(--muted2)}
            .features{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:16px}
            .feat{
                padding:14px;border-radius:18px;border:1px solid var(--line);background:rgba(0,0,0,.12);
                transition:transform .15s ease, background .15s ease, border-color .15s ease;
            }
            .feat:hover{transform:translateY(-1px);background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.14)}
            .feat b{display:block;margin-bottom:6px}
            .muted2{color:var(--muted2);font-size:13px;line-height:1.55}
            .list{margin:10px 0 0;padding:0;list-style:none;display:grid;gap:10px}
            .li{display:flex;gap:10px;color:var(--muted)}
            .pillDot{width:10px;height:10px;border-radius:999px;margin-top:6px;flex:0 0 auto}
            .links{display:grid;gap:10px;margin-top:14px}
            .linkCard{
                border:1px solid var(--line);background:rgba(0,0,0,.12);
                border-radius:18px;padding:14px;
                transition:transform .15s ease, background .15s ease, border-color .15s ease;
            }
            .linkCard:hover{transform:translateY(-1px);background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.14)}
            .row{display:flex;align-items:center;justify-content:space-between;gap:10px}
            .row b{font-size:14px}
            .ext{color:var(--muted2)}
            .sep{height:1px;background:var(--line);margin:16px 0}
            .footer{
                margin-top:18px;padding-top:16px;border-top:1px solid var(--line);
                display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;
                color:var(--muted2);font-size:13px
            }
            @media (max-width: 980px){
                .main{grid-template-columns:1fr}
                .heroTitle{font-size:38px}
                .features{grid-template-columns:1fr}
            }
            @media (prefers-reduced-motion: no-preference){
                .floaty{animation: floaty 7s ease-in-out infinite}
                @keyframes floaty{0%,100%{transform:translateY(0)}50%{transform:translateY(-4px)}}
            }
        </style>
    @endif
</head>

<body class="min-h-screen bg-neutral-950 text-neutral-50 antialiased">
<a class="skip" href="#content">Skip to content</a>

{{-- Ambient background ornaments (Tailwind-first; fallback-safe) --}}
<div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
    <div class="absolute -top-28 -left-28 h-[460px] w-[460px] rounded-full bg-red-600/20 blur-3xl"></div>
    <div class="absolute -top-24 -right-28 h-[420px] w-[420px] rounded-full bg-amber-400/15 blur-3xl"></div>
    <div class="absolute bottom-[-180px] left-1/2 h-[560px] w-[560px] -translate-x-1/2 rounded-full bg-sky-500/10 blur-3xl"></div>
</div>

<div class="relative mx-auto max-w-6xl px-6 py-6 lg:px-8 wrap">
    {{-- Top Nav --}}
    <header class="flex items-center justify-between gap-3 nav">
        <div class="flex items-center gap-3 brand">
            <div class="h-11 w-11 rounded-2xl bg-gradient-to-br from-red-700 to-amber-400 shadow-2xl shadow-black/40 flex items-center justify-center logo floaty">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 2l2.4 6.7 7.1.1-5.7 4.1 2.2 6.8L12 15.9 6 19.7l2.2-6.8L2.5 8.8l7.1-.1L12 2z" stroke="currentColor" stroke-width="1.6" class="text-white"/>
                </svg>
            </div>
            <div>
                <div class="text-sm text-neutral-400 kicker">Welcome to</div>
                <div class="text-lg font-semibold tracking-tight name">
                    {{ config('app.name', 'RateIt') }}
                </div>
            </div>
        </div>

        <div class="hidden items-center gap-2 sm:flex">
            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-black/20 px-3 py-1.5 text-xs font-medium text-neutral-300 badge">
                <span class="inline-block h-2 w-2 rounded-full bg-emerald-400 dot"></span>
                Backend running • {{ config('app.env') }}
            </span>
        </div>

        @if (Route::has('login'))
            <nav class="flex items-center gap-2 navlinks" aria-label="Primary">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium hover:bg-white/10 transition btn">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium hover:bg-white/10 transition btn">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-br from-red-700 to-red-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-red-900/30 hover:opacity-95 transition btn btnPrimary">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    {{-- Content --}}
    <main id="content" class="mt-10 grid gap-6 lg:grid-cols-12 main">
        {{-- Left: Main pitch --}}
        <section class="lg:col-span-7">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/40 backdrop-blur-xl lg:p-10 card pad">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-black/20 px-4 py-2 text-xs font-medium text-neutral-300 badge">
                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                    Laravel 12 • Clean Architecture • Unified API Responses
                </div>

                <h1 class="mt-5 text-4xl font-semibold tracking-tight text-white lg:text-5xl heroTitle">
                    <span class="grad">Rate the world.</span> Earn rewards. <span class="text-neutral-300">Build trust.</span>
                </h1>

                <p class="mt-4 text-base leading-relaxed text-neutral-300 sub">
                    RateIt is a multi-platform rating ecosystem (User / Admin / Vendor) designed for authentic reviews,
                    onboarding experiences, and secure authentication with a clean, scalable backend structure.
                </p>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center cta">
                    <a href="{{ url('/api') }}"
                       class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-br from-red-700 to-red-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/30 hover:opacity-95 transition btn btnPrimary"
                       aria-label="Explore APIs">
                        Explore APIs
                        <svg class="ml-2 h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 17L17 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M9 7h8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </a>

                    <a href="{{ url('/dashboard') }}"
                       class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-5 py-3 text-sm font-medium text-white hover:bg-white/10 transition btn">
                        Open Dashboard
                    </a>

                    <div class="text-xs text-neutral-400 hint">
                        Tip: import the Postman collection to test v1 endpoints fast.
                    </div>
                </div>

                <div class="mt-8 grid gap-3 sm:grid-cols-3 features">
                    <div class="rounded-2xl border border-white/10 bg-black/10 p-4 feat">
                        <div class="text-sm font-semibold">User App</div>
                        <div class="mt-1 text-sm text-neutral-300">Onboarding, auth, reviews, rewards.</div>
                        <div class="mt-2 text-xs text-neutral-500">AR/EN ready</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-black/10 p-4 feat">
                        <div class="text-sm font-semibold">Vendor</div>
                        <div class="mt-1 text-sm text-neutral-300">Branches, QR flow, campaigns, stats.</div>
                        <div class="mt-2 text-xs text-neutral-500">Operational tools</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-black/10 p-4 feat">
                        <div class="text-sm font-semibold">Admin</div>
                        <div class="mt-1 text-sm text-neutral-300">Moderation, dashboards, control center.</div>
                        <div class="mt-2 text-xs text-neutral-500">Governance</div>
                    </div>
                </div>

                <div class="mt-8 h-px w-full bg-white/10 sep"></div>

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
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/40 backdrop-blur-xl lg:p-8 card pad">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs text-neutral-400">Quickstart</div>
                        <div class="text-lg font-semibold">Get productive fast</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-black/20 px-3 py-2 text-xs text-neutral-300">
                        v1 APIs
                    </div>
                </div>

                <div class="mt-5 space-y-3 links">
                    <a href="https://laravel.com/docs" target="_blank" rel="noreferrer"
                       class="group block rounded-2xl border border-white/10 bg-black/10 p-4 hover:bg-white/5 transition linkCard">
                        <div class="flex items-center justify-between row">
                            <div class="font-medium">Laravel Documentation</div>
                            <span class="text-neutral-400 group-hover:text-white transition ext">↗</span>
                        </div>
                        <div class="mt-1 text-sm text-neutral-300">
                            Routing, validation, resources, middleware.
                        </div>
                    </a>

                    <a href="https://laracasts.com" target="_blank" rel="noreferrer"
                       class="group block rounded-2xl border border-white/10 bg-black/10 p-4 hover:bg-white/5 transition linkCard">
                        <div class="flex items-center justify-between row">
                            <div class="font-medium">Laracasts</div>
                            <span class="text-neutral-400 group-hover:text-white transition ext">↗</span>
                        </div>
                        <div class="mt-1 text-sm text-neutral-300">
                            Practical tutorials to speed up development.
                        </div>
                    </a>

                    <div class="rounded-2xl border border-white/10 bg-black/10 p-4">
                        <div class="font-medium">Suggested flow</div>
                        <ul class="mt-3 space-y-2 text-sm text-neutral-300 list">
                            <li class="flex gap-2 li">
                                <span class="mt-1 inline-block h-2 w-2 rounded-full bg-red-500 pillDot"></span>
                                Run migrations + seeders (onboarding screens, etc.)
                            </li>
                            <li class="flex gap-2 li">
                                <span class="mt-1 inline-block h-2 w-2 rounded-full bg-amber-400 pillDot"></span>
                                Import Postman collection & environment
                            </li>
                            <li class="flex gap-2 li">
                                <span class="mt-1 inline-block h-2 w-2 rounded-full bg-emerald-400 pillDot"></span>
                                Test Auth endpoints (register/login/me/logout)
                            </li>
                        </ul>

                        <div class="mt-4 rounded-xl border border-white/10 bg-black/20 p-3 text-xs text-neutral-400">
                            Recommended: keep a “dev” Postman env with base_url, token, locale, device_id.
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ url('/api') }}"
                       class="inline-flex w-full items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-medium hover:bg-white/10 transition btn">
                        API Index
                    </a>
                    <a href="{{ url('/dashboard') }}"
                       class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-red-600 px-4 py-3 text-sm font-semibold text-black hover:opacity-95 transition btn"
                       aria-label="Deploy ready">
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
    <footer class="mt-10 flex flex-col gap-3 border-t border-white/10 pt-6 text-sm text-neutral-400 sm:flex-row sm:items-center sm:justify-between footer">
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
