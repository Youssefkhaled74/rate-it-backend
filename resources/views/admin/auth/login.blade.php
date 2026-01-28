@extends('admin.layouts.auth')
@section('title', 'Admin Login')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-10
            bg-[radial-gradient(1200px_600px_at_20%_10%,rgba(185,28,28,.12),transparent_60%),radial-gradient(900px_500px_at_90%_20%,rgba(0,0,0,.10),transparent_55%),linear-gradient(to_br,rgba(243,244,246,1),rgba(229,231,235,1))]">

    {{-- subtle grid --}}
    <div class="pointer-events-none fixed inset-0 opacity-[0.12]"
        style="background-image: linear-gradient(to right, rgba(0,0,0,.08) 1px, transparent 1px),
              linear-gradient(to bottom, rgba(0,0,0,.08) 1px, transparent 1px);
              background-size: 44px 44px;">
    </div>

    <div class="w-full max-w-md relative">

        {{-- Card --}}
        <div class="relative bg-white/75 backdrop-blur-xl rounded-3xl
                shadow-[0_30px_90px_rgba(0,0,0,.14)]
                border border-white/60
                p-8 overflow-hidden">

            {{-- soft glow blobs --}}
            <div class="pointer-events-none absolute -top-28 -right-28 w-72 h-72 rounded-full bg-red-200/40 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-28 -left-28 w-72 h-72 rounded-full bg-gray-200/70 blur-3xl"></div>

            <div class="relative">

                {{-- Brand --}}
                <div class="flex items-center justify-center mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-red-800 border border-red-700/40 grid place-items-center
            shadow-lg shadow-red-800/25 overflow-hidden">
                            <img
                                src="{{ asset('assets/images/Vector.png') }}"
                                alt="Rateit Logo"
                                class="w-8 h-8 object-contain">
                        </div>
                        <div>
                            <div class="text-xl font-semibold leading-tight">Rateit</div>
                            <div class="text-xs text-gray-500">Admin Dashboard</div>
                        </div>
                    </div>
                </div>

                {{-- Heading --}}
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Welcome back</h1>
                    <p class="text-sm text-gray-500 mt-1">Sign in to continue</p>
                </div>

                {{-- Error --}}
                @if ($errors->any())
                <div class="mb-5 rounded-2xl bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3">
                    {{ $errors->first() }}
                </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700">Email address</label>
                        <div class="mt-2 relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                {{-- mail icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 4h16v16H4z"></path>
                                    <path d="m4 6 8 7 8-7"></path>
                                </svg>
                            </span>
                            <input
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                placeholder="admin@rateit.com"
                                class="w-full rounded-2xl border border-gray-200 bg-white/90 pl-11 pr-4 py-3
                       text-sm outline-none transition
                       focus:border-red-400 focus:ring-4 focus:ring-red-100">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-2 relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                {{-- lock icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M7 11V8a5 5 0 0 1 10 0v3"></path>
                                    <path d="M6 11h12v10H6z"></path>
                                </svg>
                            </span>

                            <input
                                id="admin_password"
                                name="password"
                                type="password"
                                required
                                placeholder="••••••••"
                                class="w-full rounded-2xl border border-gray-200 bg-white/90 pl-11 pr-12 py-3
                       text-sm outline-none transition
                       focus:border-red-400 focus:ring-4 focus:ring-red-100">

                            {{-- Professional show/hide --}}
                            <button
                                type="button"
                                id="toggle_password"
                                class="absolute inset-y-0 right-2 px-3 rounded-xl
                       text-gray-500 hover:text-gray-900 hover:bg-gray-50/70 transition"
                                aria-label="Toggle password visibility">
                                {{-- eye --}}
                                <svg id="eye_open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>

                                {{-- eye-off --}}
                                <svg id="eye_closed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 3l18 18"></path>
                                    <path d="M10.6 10.6A3 3 0 0 0 12 15a3 3 0 0 0 2.12-.88"></path>
                                    <path d="M9.9 5.2A10.5 10.5 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-3.2 4.4"></path>
                                    <path d="M6.2 6.2C3.3 8.3 2 12 2 12s3.5 7 10 7c1.3 0 2.5-.2 3.6-.6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="remember" value="1"
                                class="rounded-md border-gray-300 text-red-700 focus:ring-red-500">
                            Remember me
                        </label>
                        <span class="text-xs text-gray-400">Secured access</span>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full rounded-2xl bg-red-800 text-white py-3.5
                   text-sm font-semibold tracking-wide
                   shadow-lg shadow-red-800/25
                   hover:bg-red-900 hover:shadow-red-900/30
                   focus:outline-none focus:ring-4 focus:ring-red-200
                   transition">
                        Login
                    </button>
                </form>
            </div>

            {{-- Inline JS: Show/Hide password --}}
            <script>
                (function() {
                    const input = document.getElementById('admin_password');
                    const btn = document.getElementById('toggle_password');
                    const eyeOpen = document.getElementById('eye_open');
                    const eyeClosed = document.getElementById('eye_closed');
                    if (!input || !btn) return;

                    btn.addEventListener('click', function() {
                        const isHidden = input.type === 'password';
                        input.type = isHidden ? 'text' : 'password';
                        eyeOpen.classList.toggle('hidden', isHidden);
                        eyeClosed.classList.toggle('hidden', !isHidden);
                    });
                })();
            </script>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-500 mt-6">
            © {{ date('Y') }} Rateit Platform
        </p>
    </div>
</div>
@endsection