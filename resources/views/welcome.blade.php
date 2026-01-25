{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="en" dir="ltr" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0b0b">
    <title>{{ config('app.name', 'Rate It') }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        /* Works even if Tailwind build isn't present (minimal extras only) */
        .no-select { -webkit-user-select:none; user-select:none; }
    </style>
</head>

<body class="min-h-screen antialiased bg-neutral-50 text-neutral-900 dark:bg-neutral-950 dark:text-neutral-50">
    {{-- Background --}}
    <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-24 -left-24 h-[460px] w-[460px] rounded-full bg-red-600/10 blur-3xl dark:bg-red-600/20"></div>
        <div class="absolute -top-24 -right-24 h-[420px] w-[420px] rounded-full bg-amber-400/10 blur-3xl dark:bg-amber-400/15"></div>
        <div class="absolute bottom-[-180px] left-1/2 h-[560px] w-[560px] -translate-x-1/2 rounded-full bg-sky-500/10 blur-3xl dark:bg-sky-500/10"></div>
    </div>

    <div class="relative mx-auto max-w-6xl px-5 py-6 lg:px-8">
        {{-- Top Bar --}}
        <header class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-2xl bg-gradient-to-br from-red-700 to-amber-400 shadow-2xl shadow-black/20 dark:shadow-black/40 flex items-center justify-center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 2l2.4 6.7 7.1.1-5.7 4.1 2.2 6.8L12 15.9 6 19.7l2.2-6.8L2.5 8.8l7.1-.1L12 2z"
                              stroke="currentColor" stroke-width="1.6" class="text-white"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-neutral-500 dark:text-neutral-400" data-i18n="welcome">Welcome to</div>
                    <div class="text-lg font-semibold tracking-tight">
                        {{ config('app.name', 'Rate It') }}
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Language Toggle --}}
                <button id="langBtn"
                    class="no-select inline-flex items-center gap-2 rounded-2xl border border-black/10 bg-white/60 px-4 py-2 text-sm font-medium shadow-sm hover:bg-white/80
                           dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    <span id="langLabel">EN</span>
                </button>

                {{-- Theme Toggle --}}
                <button id="themeBtn"
                    class="no-select inline-flex items-center gap-2 rounded-2xl border border-black/10 bg-white/60 px-4 py-2 text-sm font-medium shadow-sm hover:bg-white/80
                           dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10">
                    <span id="themeIcon">🌙</span>
                    <span data-i18n="theme">Theme</span>
                </button>
            </div>
        </header>

        {{-- Hero --}}
        <main class="mt-10 grid gap-6 lg:grid-cols-12">
            <section class="lg:col-span-7">
                <div class="rounded-3xl border border-black/10 bg-white/60 p-6 shadow-2xl shadow-black/10 backdrop-blur-xl
                            dark:border-white/10 dark:bg-white/5 dark:shadow-black/40 lg:p-10">
                    <div class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/60 px-4 py-2 text-xs font-semibold text-neutral-700
                                dark:border-white/10 dark:bg-black/20 dark:text-neutral-300">
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                        <span data-i18n="taglineBadge">Real reviews • Rewards • Trust</span>
                    </div>

                    <h1 class="mt-5 text-4xl font-semibold tracking-tight lg:text-5xl">
                        <span data-i18n="heroTitle1">Rate places.</span>
                        <span data-i18n="heroTitle2">Earn rewards.</span>
                        <span class="text-neutral-600 dark:text-neutral-300" data-i18n="heroTitle3">Build trust.</span>
                    </h1>

                    <p class="mt-4 text-base leading-relaxed text-neutral-700 dark:text-neutral-300" data-i18n="heroDesc">
                        Discover places, read trusted reviews, then rate after visiting by scanning a QR code. Earn points and redeem vouchers.
                    </p>

                    {{-- Primary CTAs --}}
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a href="{{ url('/app') }}"
                           class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-br from-red-700 to-red-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/20 hover:opacity-95 transition">
                            <span data-i18n="ctaUser">Continue as User</span>
                        </a>

                        <a href="{{ url('/vendor') }}"
                           class="inline-flex items-center justify-center rounded-2xl border border-black/10 bg-white/60 px-5 py-3 text-sm font-semibold text-neutral-900 hover:bg-white/80 transition
                                  dark:border-white/10 dark:bg-white/5 dark:text-white dark:hover:bg-white/10">
                            <span data-i18n="ctaVendor">Continue as Vendor</span>
                        </a>

                        <div class="text-xs text-neutral-500 dark:text-neutral-400" data-i18n="ctaHint">
                            One platform for users & brands.
                        </div>
                    </div>

                    {{-- How it works --}}
                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-black/10 bg-white/50 p-4 dark:border-white/10 dark:bg-black/20">
                            <div class="text-sm font-semibold" data-i18n="step1Title">Scan QR</div>
                            <div class="mt-1 text-sm text-neutral-700 dark:text-neutral-300" data-i18n="step1Desc">
                                After visiting, scan the branch QR to open rating.
                            </div>
                        </div>
                        <div class="rounded-2xl border border-black/10 bg-white/50 p-4 dark:border-white/10 dark:bg-black/20">
                            <div class="text-sm font-semibold" data-i18n="step2Title">Rate & Review</div>
                            <div class="mt-1 text-sm text-neutral-700 dark:text-neutral-300" data-i18n="step2Desc">
                                Submit your rating and optional comment/photos.
                            </div>
                        </div>
                        <div class="rounded-2xl border border-black/10 bg-white/50 p-4 dark:border-white/10 dark:bg-black/20">
                            <div class="text-sm font-semibold" data-i18n="step3Title">Get Rewards</div>
                            <div class="mt-1 text-sm text-neutral-700 dark:text-neutral-300" data-i18n="step3Desc">
                                Earn points and redeem vouchers from brands.
                            </div>
                        </div>
                    </div>

                    <div class="mt-7 h-px w-full bg-black/10 dark:bg-white/10"></div>

                    {{-- Trust strip --}}
                    <div class="mt-5 flex flex-wrap items-center gap-3 text-sm">
                        <span class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/50 px-3 py-1.5 text-neutral-700
                                     dark:border-white/10 dark:bg-white/5 dark:text-neutral-300">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            <span data-i18n="pill1">Verified visit (QR)</span>
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/50 px-3 py-1.5 text-neutral-700
                                     dark:border-white/10 dark:bg-white/5 dark:text-neutral-300">
                            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                            <span data-i18n="pill2">Points → Vouchers</span>
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-black/10 bg-white/50 px-3 py-1.5 text-neutral-700
                                     dark:border-white/10 dark:bg-white/5 dark:text-neutral-300">
                            <span class="h-2 w-2 rounded-full bg-sky-400"></span>
                            <span data-i18n="pill3">Vendor verification</span>
                        </span>
                    </div>
                </div>
            </section>

            {{-- Right: Two cards (User / Vendor) --}}
            <aside class="lg:col-span-5 space-y-4">
                {{-- User card --}}
                <div class="rounded-3xl border border-black/10 bg-white/60 p-6 shadow-2xl shadow-black/10 backdrop-blur-xl
                            dark:border-white/10 dark:bg-white/5 dark:shadow-black/40 lg:p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-neutral-500 dark:text-neutral-400" data-i18n="userCardKicker">For Users</div>
                            <div class="text-lg font-semibold" data-i18n="userCardTitle">Discover. Rate. Save.</div>
                        </div>
                        <div class="rounded-2xl border border-black/10 bg-white/70 px-3 py-2 text-xs text-neutral-700
                                    dark:border-white/10 dark:bg-black/20 dark:text-neutral-300" data-i18n="userCardBadge">
                            Mobile App
                        </div>
                    </div>

                    <ul class="mt-4 space-y-2 text-sm text-neutral-700 dark:text-neutral-300">
                        <li class="flex gap-2"><span class="mt-2 h-2 w-2 rounded-full bg-emerald-400"></span><span data-i18n="userBullet1">Browse places and reviews</span></li>
                        <li class="flex gap-2"><span class="mt-2 h-2 w-2 rounded-full bg-amber-400"></span><span data-i18n="userBullet2">Rate after scanning QR</span></li>
                        <li class="flex gap-2"><span class="mt-2 h-2 w-2 rounded-full bg-sky-400"></span><span data-i18n="userBullet3">Earn points & redeem vouchers</span></li>
                    </ul>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <a href="{{ url('/app/download') }}"
                           class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-red-600 px-4 py-3 text-sm font-semibold text-black hover:opacity-95 transition"
                           data-i18n="downloadApp">
                            Download App
                        </a>
                        <a href="{{ url('/app') }}"
                           class="inline-flex items-center justify-center rounded-2xl border border-black/10 bg-white/60 px-4 py-3 text-sm font-semibold hover:bg-white/80 transition
                                  dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10"
                           data-i18n="explorePlaces">
                            Explore Places
                        </a>
                    </div>
                </div>

                {{-- Vendor card --}}
                <div class="rounded-3xl border border-black/10 bg-white/60 p-6 shadow-2xl shadow-black/10 backdrop-blur-xl
                            dark:border-white/10 dark:bg-white/5 dark:shadow-black/40 lg:p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-neutral-500 dark:text-neutral-400" data-i18n="vendorCardKicker">For Vendors</div>
                            <div class="text-lg font-semibold" data-i18n="vendorCardTitle">Grow with trusted feedback</div>
                        </div>
                        <div class="rounded-2xl border border-black/10 bg-white/70 px-3 py-2 text-xs text-neutral-700
                                    dark:border-white/10 dark:bg-black/20 dark:text-neutral-300" data-i18n="vendorCardBadge">
                            Web Panel
                        </div>
                    </div>

                    <ul class="mt-4 space-y-2 text-sm text-neutral-700 dark:text-neutral-300">
                        <li class="flex gap-2"><span class="mt-2 h-2 w-2 rounded-full bg-emerald-400"></span><span data-i18n="vendorBullet1">See reviews and insights for your branches</span></li>
                        <li class="flex gap-2"><span class="mt-2 h-2 w-2 rounded-full bg-amber-400"></span><span data-i18n="vendorBullet2">Control review frequency per branch</span></li>
                        <li class="flex gap-2"><span class="mt-2 h-2 w-2 rounded-full bg-sky-400"></span><span data-i18n="vendorBullet3">Verify & redeem vouchers (branch staff)</span></li>
                    </ul>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <a href="{{ url('/vendor/login') }}"
                           class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-br from-red-700 to-red-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-red-900/20 hover:opacity-95 transition"
                           data-i18n="vendorLogin">
                            Vendor Login
                        </a>
                        <a href="{{ url('/vendor/verify') }}"
                           class="inline-flex items-center justify-center rounded-2xl border border-black/10 bg-white/60 px-4 py-3 text-sm font-semibold hover:bg-white/80 transition
                                  dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10"
                           data-i18n="verifyVoucher">
                            Verify Voucher
                        </a>
                    </div>
                </div>
            </aside>
        </main>

        {{-- Footer --}}
        <footer class="mt-10 border-t border-black/10 pt-6 text-sm text-neutral-600 dark:border-white/10 dark:text-neutral-400">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2">
                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                    <span data-i18n="footerLeft">Trusted reviews. Real rewards.</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ url('/terms') }}" class="hover:underline" data-i18n="terms">Terms</a>
                    <span class="text-neutral-400 dark:text-neutral-600">•</span>
                    <a href="{{ url('/support') }}" class="hover:underline" data-i18n="support">Support</a>
                    <span class="text-neutral-400 dark:text-neutral-600">•</span>
                    <span>&copy; {{ date('Y') }} {{ config('app.name', 'Rate It') }}</span>
                </div>
            </div>
        </footer>
    </div>

<script>
(() => {
  // --- i18n dictionary (single page) ---
  const dict = {
    en: {
      welcome: "Welcome to",
      theme: "Theme",
      taglineBadge: "Real reviews • Rewards • Trust",
      heroTitle1: "Rate places.",
      heroTitle2: "Earn rewards.",
      heroTitle3: "Build trust.",
      heroDesc: "Discover places, read trusted reviews, then rate after visiting by scanning a QR code. Earn points and redeem vouchers.",
      ctaUser: "Continue as User",
      ctaVendor: "Continue as Vendor",
      ctaHint: "One platform for users & brands.",
      step1Title: "Scan QR",
      step1Desc: "After visiting, scan the branch QR to open rating.",
      step2Title: "Rate & Review",
      step2Desc: "Submit your rating and optional comment/photos.",
      step3Title: "Get Rewards",
      step3Desc: "Earn points and redeem vouchers from brands.",
      pill1: "Verified visit (QR)",
      pill2: "Points → Vouchers",
      pill3: "Vendor verification",

      userCardKicker: "For Users",
      userCardTitle: "Discover. Rate. Save.",
      userCardBadge: "Mobile App",
      userBullet1: "Browse places and reviews",
      userBullet2: "Rate after scanning QR",
      userBullet3: "Earn points & redeem vouchers",
      downloadApp: "Download App",
      explorePlaces: "Explore Places",

      vendorCardKicker: "For Vendors",
      vendorCardTitle: "Grow with trusted feedback",
      vendorCardBadge: "Web Panel",
      vendorBullet1: "See reviews and insights for your branches",
      vendorBullet2: "Control review frequency per branch",
      vendorBullet3: "Verify & redeem vouchers (branch staff)",
      vendorLogin: "Vendor Login",
      verifyVoucher: "Verify Voucher",

      footerLeft: "Trusted reviews. Real rewards.",
      terms: "Terms",
      support: "Support"
    },
    ar: {
      welcome: "مرحباً بك في",
      theme: "المظهر",
      taglineBadge: "تقييمات حقيقية • مكافآت • ثقة",
      heroTitle1: "قيّم الأماكن.",
      heroTitle2: "اكسب مكافآت.",
      heroTitle3: "وابنِ الثقة.",
      heroDesc: "اكتشف الأماكن واقرأ تقييمات موثوقة، ثم قيّم بعد الزيارة عبر مسح QR. اكسب نقاط واستبدلها بقسائم.",
      ctaUser: "الدخول كمستخدم",
      ctaVendor: "الدخول كبائع",
      ctaHint: "منصة واحدة للمستخدمين والبراندات.",
      step1Title: "امسح QR",
      step1Desc: "بعد الزيارة امسح QR الخاص بالفرع لفتح شاشة التقييم.",
      step2Title: "قيّم واكتب رأيك",
      step2Desc: "أرسل تقييمك مع تعليق/صور اختيارياً.",
      step3Title: "احصل على مكافآت",
      step3Desc: "اكسب نقاط واستبدلها بقسائم من البراندات.",
      pill1: "زيارة مؤكدة (QR)",
      pill2: "نقاط ← قسائم",
      pill3: "تحقق الفيندور",

      userCardKicker: "للمستخدمين",
      userCardTitle: "اكتشف. قيّم. وفّر.",
      userCardBadge: "تطبيق موبايل",
      userBullet1: "تصفّح الأماكن والتقييمات",
      userBullet2: "قيّم بعد مسح QR",
      userBullet3: "اكسب نقاط واستبدلها بقسائم",
      downloadApp: "تحميل التطبيق",
      explorePlaces: "استكشف الأماكن",

      vendorCardKicker: "للبائعين",
      vendorCardTitle: "نمُ مع ملاحظات موثوقة",
      vendorCardBadge: "لوحة ويب",
      vendorBullet1: "عرض التقييمات والإحصاءات لفروعك",
      vendorBullet2: "تحديد المدة بين التقييمات لكل فرع",
      vendorBullet3: "التحقق من القسائم واستبدالها (موظف الفرع)",
      vendorLogin: "تسجيل دخول البائع",
      verifyVoucher: "التحقق من القسيمة",

      footerLeft: "تقييمات موثوقة. مكافآت حقيقية.",
      terms: "الشروط والأحكام",
      support: "الدعم"
    }
  };

  const html = document.documentElement;
  const langBtn = document.getElementById("langBtn");
  const langLabel = document.getElementById("langLabel");
  const themeBtn = document.getElementById("themeBtn");
  const themeIcon = document.getElementById("themeIcon");

  // --- Theme ---
  const applyTheme = (mode) => {
    const isDark = mode === "dark";
    html.classList.toggle("dark", isDark);
    themeIcon.textContent = isDark ? "🌙" : "☀️";
    localStorage.setItem("theme", mode);
  };

  const storedTheme = localStorage.getItem("theme");
  if (storedTheme) applyTheme(storedTheme);
  else {
    // default: follow system
    const prefersDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;
    applyTheme(prefersDark ? "dark" : "light");
  }

  themeBtn.addEventListener("click", () => {
    applyTheme(html.classList.contains("dark") ? "light" : "dark");
  });

  // --- Language ---
  const applyLang = (lang) => {
    const pack = dict[lang] || dict.en;
    html.lang = lang;
    html.dir = lang === "ar" ? "rtl" : "ltr";
    langLabel.textContent = lang.toUpperCase();
    document.querySelectorAll("[data-i18n]").forEach(el => {
      const key = el.getAttribute("data-i18n");
      if (pack[key]) el.textContent = pack[key];
    });
    localStorage.setItem("lang", lang);
  };

  const storedLang = localStorage.getItem("lang");
  applyLang(storedLang || "en");

  langBtn.addEventListener("click", () => {
    applyLang(html.lang === "en" ? "ar" : "en");
  });
})();
</script>
</body>
</html>
