<div class="flex items-center justify-between">
    <div>
        <h1 class="text-lg font-semibold">@yield('page_title', __('admin.dashboard'))</h1>
    </div>

    <div class="flex items-center gap-3">
        {{-- Language Toggle --}}
        @php $nextLang = app()->getLocale() === 'ar' ? 'en' : 'ar'; @endphp
        <a href="{{ route('admin.lang.switch', $nextLang) }}"
           class="h-11 px-4 rounded-full bg-white border border-gray-200 grid place-items-center text-sm font-semibold text-gray-700 shadow-sm hover:shadow-md transition">
            {{ strtoupper($nextLang) }}
        </a>

        {{-- Settings --}}
        <button type="button"
            class="w-11 h-11 rounded-full bg-white border border-gray-200 grid place-items-center
           shadow-sm hover:shadow-md transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-800" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 15.5A3.5 3.5 0 1 0 12 8.5a3.5 3.5 0 0 0 0 7z" />
                <path d="M19.4 15a7.9 7.9 0 0 0 .1-2l2-1.2-2-3.4-2.3.6a8 8 0 0 0-1.7-1L15 3h-6l-.5 3a8 8 0 0 0-1.7 1L4.5 6.4l-2 3.4 2 1.2a8 8 0 0 0 0 2l-2 1.2 2 3.4 2.3-.6a8 8 0 0 0 1.7 1L9 21h6l.5-3a8 8 0 0 0 1.7-1l2.3.6 2-3.4-2.1-1.2z" />
            </svg>
        </button>

        {{-- Notifications --}}
        <button type="button"
            class="w-11 h-11 rounded-full bg-white border border-gray-200 grid place-items-center
           shadow-sm hover:shadow-md transition relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-800" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7" />
                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>

            {{-- optional tiny badge --}}
            <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-red-600"></span>
        </button>

                {{-- Admin avatar / profile link --}}
                @php $me = auth()->guard('admin_web')->user(); @endphp
                <a href="{{ route('admin.profile.edit') }}" title="{{ __('admin.profile') }}"
                     class="w-10 h-10 rounded-full bg-white border border-gray-200 grid place-items-center overflow-hidden">
                    @if($me && $me->photo_url)
                        <img src="{{ $me->photo_url }}" alt="{{ $me->name }}" class="w-10 h-10 object-cover">
                    @else
                        <div class="w-10 h-10 grid place-items-center bg-gray-100 text-gray-700 font-semibold">
                            {{ $me ? strtoupper(substr($me->name,0,1)) : 'A' }}
                        </div>
                    @endif
                </a>
    </div>
</div>
