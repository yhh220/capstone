<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
@php
    use Artesaos\SEOTools\Facades\SEOMeta;
    use Artesaos\SEOTools\Facades\OpenGraph;
    use Artesaos\SEOTools\Facades\TwitterCard;
    use Artesaos\SEOTools\Facades\JsonLd;
    $storeName = config('services.store.name');
    $storeShortName = config('services.store.short_name');
    $storeTagline = config('services.store.tagline');
    $storePhoneDisplay = config('services.store.phone_display');
    $storePhoneRaw = config('services.store.phone_raw');
    $storeEmail = config('services.store.email');
    $storeFacebookUrl = config('services.store.facebook_url');
    $storeAddress = config('services.store.address');
    $storeHours = config('services.store.hours');
    $whatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hello, I would like to ask about your products and showroom visit.');
    $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($storeAddress);
    $telLink = 'tel:' . preg_replace('/[^0-9+]/', '', $storePhoneDisplay);
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#E11D48" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0C0C0E" media="(prefers-color-scheme: dark)">
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! TwitterCard::generate() !!}
    {!! JsonLd::generate() !!}

    <script>
        (function () {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red: 'rgb(var(--brand-red-rgb) / <alpha-value>)',
                            yellow: 'rgb(var(--brand-yellow-rgb) / <alpha-value>)',
                            black: 'rgb(var(--brand-black-rgb) / <alpha-value>)',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* ── Carbon Heat color theme ───────────────────────── */
        :root {
            --brand-red-rgb: 225 29 72;       /* #E11D48 — modern crimson */
            --brand-yellow-rgb: 245 158 11;   /* #F59E0B — amber accent  */
            --brand-black-rgb: 24 24 27;      /* #18181B — neutral zinc  */
        }
        .dark {
            --brand-red-rgb: 251 113 133;     /* #FB7185 — softened rose for dark */
            --brand-yellow-rgb: 251 191 36;   /* #FBBF24 — brighter amber       */
            --brand-black-rgb: 26 26 30;      /* #1A1A1E — elevated carbon panel */
        }

        /* True carbon-black body in dark mode */
        .dark body {
            background-color: #0C0C0E;
            color: #FAFAFA;
        }

        :root {
            --app-bg-rgb: 255 255 255;
            --app-surface-rgb: 255 255 255;
            --app-surface-soft-rgb: 244 244 245;
            --app-border-rgb: 228 228 231;
            --app-text-rgb: 39 39 42;
            --app-muted-rgb: 82 82 91;
        }

        .dark {
            --app-bg-rgb: 12 12 14;
            --app-surface-rgb: 26 26 30;
            --app-surface-soft-rgb: 20 20 24;
            --app-border-rgb: 42 42 48;
            --app-text-rgb: 250 250 250;
            --app-muted-rgb: 212 212 216;
        }

        body {
            background-color: rgb(var(--app-bg-rgb));
            color: rgb(var(--app-text-rgb));
            transition: background-color 0.2s, color 0.2s;
        }

        .dark .dark\:bg-gray-900 {
            background-color: rgb(var(--app-bg-rgb)) !important;
        }

        .dark .dark\:bg-gray-800,
        .dark .dark\:bg-gray-700 {
            background-color: rgb(var(--app-surface-rgb)) !important;
        }

        .dark .dark\:border-gray-700,
        .dark .dark\:border-gray-600 {
            border-color: rgb(var(--app-border-rgb)) !important;
        }

        .dark .dark\:text-white,
        .dark .dark\:text-gray-100 {
            color: rgb(var(--app-text-rgb)) !important;
        }

        .dark .dark\:text-gray-200,
        .dark .dark\:text-gray-300 {
            color: rgb(var(--app-muted-rgb)) !important;
        }

        .dark .dark\:text-gray-400 {
            color: rgb(161 161 170) !important;
        }

        .dark .dark\:hover\:bg-gray-700:hover,
        .dark .dark\:hover\:bg-gray-700\/50:hover {
            background-color: rgb(35 35 41) !important;
        }

        .dark .dark\:bg-red-900\/10,
        .dark .dark\:bg-red-900\/20,
        .dark .dark\:bg-red-900\/30,
        .dark .dark\:hover\:bg-red-900\/20:hover {
            background-color: rgb(75 17 31 / 0.45) !important;
        }

        .skip-link {
            position: absolute;
            top: -48px;
            left: 8px;
            background: rgb(var(--brand-red-rgb));
            color: #fff;
            padding: 10px 18px;
            border-radius: 0 0 8px 8px;
            font-weight: 700;
            font-size: 0.875rem;
            z-index: 9999;
            text-decoration: none;
            transition: top 0.15s ease;
        }

        .skip-link:focus {
            top: 0;
        }

        *:focus-visible {
            outline: 3px solid rgb(var(--brand-red-rgb));
            outline-offset: 2px;
            border-radius: 4px;
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        .hero-gradient {
            background: linear-gradient(135deg, #18181B 0%, #2A2A30 50%, rgb(var(--brand-red-rgb)) 100%);
        }
        .dark .hero-gradient {
            background: linear-gradient(135deg, #0C0C0E 0%, #1A1A1E 50%, rgb(var(--brand-red-rgb)) 100%);
        }

        /* ── Hero orb floating animation ─────────────────── */
        @keyframes floatOrb {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-28px) scale(1.06); }
        }
        @keyframes floatOrbAlt {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(22px) scale(0.95); }
        }
        .orb-float     { animation: floatOrb    8s ease-in-out infinite; }
        .orb-float-alt { animation: floatOrbAlt 11s ease-in-out infinite; }

        /* ── Page-load hero text reveal ──────────────────── */
        @keyframes heroReveal {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .hero-reveal        { animation: heroReveal 0.7s ease-out both; }
        .hero-reveal-delay1 { animation: heroReveal 0.7s 0.15s ease-out both; }
        .hero-reveal-delay2 { animation: heroReveal 0.7s 0.3s  ease-out both; }
        .hero-reveal-delay3 { animation: heroReveal 0.7s 0.45s ease-out both; }

        /* ── Card lift on hover ──────────────────────────── */
        .card-hover {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.12);
        }

        /* ── Shimmer skeleton (optional, future use) ─────── */
        @keyframes shimmer {
            from { background-position: -200% 0; }
            to   { background-position:  200% 0; }
        }

        /* ── AOS customisation ───────────────────────────── */
        [data-aos] { will-change: transform, opacity; }
    </style>

    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans antialiased">
    <x-page-loader />
    <a href="#main-content" class="skip-link">{{ __('Skip to main content') }}</a>

    @php
        $shoppingEnabled = setting('ONLINE_SHOPPING_ENABLED') === 'true';
        $cartCount = 0;
        if ($shoppingEnabled) {
            $cartCount = (int) \App\Models\CartItem::forCurrentOwner()->sum('quantity');
        }
    @endphp

    <nav x-data="{ cartOpen: false }"
         class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50 border-b border-gray-100 dark:border-gray-700"
         role="navigation"
         aria-label="{{ __('Main navigation') }}">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16 gap-2">
                <!-- Brand Identity -->
                <a href="{{ route('home') }}"
                   class="flex items-center gap-2 flex-shrink-0 group"
                   aria-label="{{ $storeName }} - {{ __('Home') }}">
                    <div class="w-10 h-10 bg-brand-red rounded-full flex items-center justify-center flex-shrink-0 transition-transform duration-300 group-hover:scale-110 group-active:scale-95 shadow-md shadow-brand-red/20" aria-hidden="true">
                        <span class="text-brand-yellow font-black text-lg">W</span>
                    </div>
                    <div class="leading-tight transition-opacity duration-300 group-hover:opacity-80">
                        <div class="font-black text-brand-black dark:text-white text-sm uppercase tracking-wide">{{ $storeShortName }}</div>
                        <div class="text-xs text-brand-red font-semibold uppercase tracking-widest">{{ $storeTagline }}</div>
                    </div>
                </a>

                <!-- Desktop Nav (7 links) -->
                <div class="hidden md:flex items-center gap-0.5" role="list">
                    @foreach([
                        [route('home'),     __('Home'),     request()->routeIs('home')],
                        [route('products'), __('Products'), request()->routeIs('products*')],
                        [route('services'), __('Services'), request()->routeIs('services')],
                        [route('gallery'),  __('Gallery'),  request()->routeIs('gallery')],
                        [route('booking'),  __('Booking'),  request()->routeIs('booking*')],
                        [route('about'),    __('About'),    request()->routeIs('about')],
                        [route('contact'),  __('Contact'),  request()->routeIs('contact')],
                    ] as [$href, $label, $active])
                    <a href="{{ $href }}"
                       class="px-2.5 py-1.5 lg:px-4 lg:py-2 rounded-full text-xs lg:text-sm font-bold transition-all duration-300 ease-out active:scale-95 whitespace-nowrap
                              {{ $active ? 'text-brand-red bg-red-50 dark:bg-red-900/10' : 'text-gray-600 dark:text-gray-300 hover:text-brand-red hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:shadow-sm' }}"
                       @if($active) aria-current="page" @endif>
                        {{ $label }}
                    </a>
                    @endforeach
                </div>

                <!-- Right-side icons: Lang, Theme, Cart (cond), User, WhatsApp, Mobile menu -->
                <div class="flex items-center gap-1.5">

                    <!-- 1. Language switcher -->
                    <div class="relative" id="lang-wrapper">
                        <button id="lang-btn"
                                aria-label="{{ __('Select language') }}"
                                aria-expanded="false"
                                aria-haspopup="true"
                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600 transition-colors">
                            @if(app()->getLocale() === 'ms')
                                BM
                            @elseif(app()->getLocale() === 'zh')
                                中文
                            @else
                                EN
                            @endif
                            <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div id="lang-menu"
                             role="menu"
                             aria-orientation="vertical"
                             class="hidden absolute right-0 top-full mt-1.5 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden w-44 z-50">
                            @foreach([
                                ['en', 'English'],
                                ['ms', 'Bahasa Melayu'],
                                ['zh', '中文'],
                            ] as [$code, $name])
                            <a href="{{ route('lang', $code) }}"
                               role="menuitem"
                               class="flex items-center gap-2 px-4 py-2.5 text-sm transition-colors {{ app()->getLocale() === $code ? 'text-brand-red font-semibold bg-red-50 dark:bg-red-900/20' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                {{ $name }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. Dark mode toggle -->
                    <div class="relative" id="theme-wrapper">
                        <button id="theme-btn"
                                aria-label="{{ __('Select theme') }}"
                                aria-expanded="false"
                                aria-haspopup="true"
                                class="flex items-center gap-1.5 p-2 rounded-lg transition-colors text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600">
                            <svg id="icon-theme-display" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                            </svg>
                        </button>
                        <div id="theme-menu"
                             role="menu"
                             aria-orientation="vertical"
                             class="hidden absolute right-0 top-full mt-1.5 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden w-36 z-50">
                            <button class="theme-option flex items-center w-full gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700" data-theme="light">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                                Light
                            </button>
                            <button class="theme-option flex items-center w-full gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700" data-theme="dark">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                                Dark
                            </button>
                            <button class="theme-option flex items-center w-full gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700" data-theme="system">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                                System
                            </button>
                        </div>
                    </div>

                    <!-- 3. Cart icon (only when shopping enabled) -->
                    @if($shoppingEnabled)
                    <button type="button"
                            @click="cartOpen = true"
                            aria-label="{{ __('Open cart') }}"
                            class="relative p-2 rounded-lg transition-colors text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 bg-brand-red text-white text-[10px] leading-none font-bold rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                        @endif
                    </button>
                    @endif

                    <!-- 4. User dropdown (desktop) -->
                    <div class="hidden md:block relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                @click.outside="open = false"
                                type="button"
                                class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-600"
                                aria-label="{{ __('User menu') }}">
                            @auth
                                <div class="w-6 h-6 rounded-full bg-brand-red flex items-center justify-center text-white text-[11px] font-black">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @else
                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            @endauth
                        </button>

                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 top-full mt-2 w-56 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">
                            @auth
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                    <div class="font-bold text-gray-800 dark:text-white text-sm truncate">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</div>
                                    <div class="text-xs text-brand-red font-semibold mt-0.5">{{ ucfirst(Auth::user()->role) }}</div>
                                </div>
                                <div class="py-1">
                                    <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ __('My Profile') }}
                                    </a>
                                    @if($shoppingEnabled)
                                    <a href="{{ route('my-orders') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        {{ __('My Orders') }}
                                    </a>
                                    <a href="{{ route('track-order') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                        {{ __('Track Order') }}
                                    </a>
                                    @endif
                                    <a href="{{ route('booking.track') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ __('Track Booking') }}
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                    <a href="/admin" class="flex items-center gap-3 px-4 py-2.5 text-sm text-brand-red font-semibold hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ __('Admin Dashboard') }}
                                    </a>
                                    @endif
                                </div>
                                <div class="border-t border-gray-100 dark:border-gray-700 py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            {{ __('Sign Out') }}
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="py-1">
                                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        {{ __('Sign In') }}
                                    </a>
                                    @if($shoppingEnabled)
                                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                        {{ __('Register') }}
                                    </a>
                                    @endif
                                </div>
                            @endauth
                        </div>
                    </div>

                    <!-- 5. WhatsApp button (always visible) -->
                    <a href="{{ $whatsAppUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex items-center justify-center p-2 rounded-lg bg-[#25D366] text-white hover:bg-[#1EBE57] transition-colors"
                       aria-label="{{ __('WhatsApp us') }}">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>

                    <!-- 6. Mobile menu button -->
                    <button id="mobile-menu-btn"
                            class="md:hidden p-2 rounded-lg transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600"
                            aria-label="{{ __('Toggle mobile menu') }}"
                            aria-expanded="false"
                            aria-controls="mobile-menu">
                        <svg id="icon-hamburger" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg id="icon-close" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu"
                 class="hidden md:hidden pb-4 space-y-1 border-t border-gray-100 dark:border-gray-700 pt-3"
                 role="menu">
                @foreach([
                    [route('home'),     __('Home'),     request()->routeIs('home')],
                    [route('products'), __('Products'), request()->routeIs('products*')],
                    [route('services'), __('Services'), request()->routeIs('services')],
                    [route('gallery'),  __('Gallery'),  request()->routeIs('gallery')],
                    [route('booking'),  __('Booking'),  request()->routeIs('booking*')],
                    [route('about'),    __('About'),    request()->routeIs('about')],
                    [route('contact'),  __('Contact'),  request()->routeIs('contact')],
                ] as [$href, $label, $active])
                <a href="{{ $href }}"
                   role="menuitem"
                   class="block py-2.5 px-3 rounded-lg font-medium transition-colors {{ $active ? 'text-brand-red bg-red-50 dark:bg-red-900/20' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-brand-red' }}"
                   @if($active) aria-current="page" @endif>
                    {{ $label }}
                </a>
                @endforeach

                <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                    @auth
                        <div class="px-3 py-2 mb-1">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-brand-red flex items-center justify-center text-white text-sm font-black">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-gray-800 dark:text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-400">{{ ucfirst(Auth::user()->role) }}</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ __('My Profile') }}
                        </a>
                        @if($shoppingEnabled)
                        <a href="{{ route('my-orders') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            {{ __('My Orders') }}
                        </a>
                        <a href="{{ route('track-order') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            {{ __('Track Order') }}
                        </a>
                        @endif
                        <a href="{{ route('booking.track') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ __('Track Booking') }}
                        </a>
                        @if(Auth::user()->isAdmin())
                        <a href="/admin" class="flex items-center gap-3 py-2.5 px-3 rounded-lg font-semibold text-brand-red hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ __('Admin Dashboard') }}
                        </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full py-2.5 px-3 rounded-lg font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                {{ __('Sign Out') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block py-2.5 px-3 rounded-lg font-medium text-brand-red hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">{{ __('Sign In') }}</a>
                        @if($shoppingEnabled)
                        <a href="{{ route('login') }}" class="block py-2.5 px-3 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">{{ __('Register') }}</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <!-- Cart Drawer (slide-out from right) -->
        @if($shoppingEnabled)
        <div x-show="cartOpen" x-cloak class="fixed inset-0 z-[60]" style="display:none;" @keydown.escape.window="cartOpen = false">
            <!-- Overlay -->
            <div x-show="cartOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="cartOpen = false"
                 class="absolute inset-0 bg-black/50"
                 aria-hidden="true"></div>
            <!-- Panel -->
            <aside x-show="cartOpen"
                   x-transition:enter="transform transition ease-out duration-300"
                   x-transition:enter-start="translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="transform transition ease-in duration-200"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="translate-x-full"
                   class="absolute right-0 top-0 h-full w-full max-w-md bg-white dark:bg-gray-800 shadow-2xl overflow-y-auto flex flex-col"
                   role="dialog"
                   aria-modal="true"
                   aria-label="{{ __('Shopping cart') }}">
                <div class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ __('Your Cart') }}</h2>
                    <button @click="cartOpen = false"
                            type="button"
                            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300"
                            aria-label="{{ __('Close cart') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="flex-1">
                    <livewire:cart-page />
                </div>
            </aside>
        </div>
        @endif
    </nav>

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-300 px-4 py-3 max-w-7xl mx-auto mt-4 rounded-r-lg" role="alert" aria-live="polite">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-300 px-4 py-3 max-w-7xl mx-auto mt-4 rounded-r-lg" role="alert" aria-live="polite">
        {{ session('error') }}
    </div>
    @endif

    <main id="main-content" tabindex="-1">
        {{ $slot }}
    </main>

    <footer class="bg-brand-black text-gray-300 mt-16" role="contentinfo">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                <div class="sm:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 bg-brand-red rounded-full flex items-center justify-center flex-shrink-0" aria-hidden="true">
                            <span class="text-brand-yellow font-black text-lg">W</span>
                        </div>
                        <div>
                            <div class="font-black text-white text-lg">{{ $storeName }}</div>
                            <div class="text-xs text-brand-yellow uppercase tracking-widest">{{ $storeTagline }}</div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        {{ __('Browse our products online, then visit the showroom or message us on WhatsApp for recommendations and installation guidance.') }}
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-white mb-4 uppercase text-sm tracking-wider">{{ __('Quick Links') }}</h4>
                    <ul class="space-y-2 text-sm" role="list">
                        <li><a href="{{ route('home') }}"     class="hover:text-brand-yellow transition-colors">{{ __('Home') }}</a></li>
                        <li><a href="{{ route('products') }}" class="hover:text-brand-yellow transition-colors">{{ __('Products') }}</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-brand-yellow transition-colors">{{ __('Services') }}</a></li>
                        <li><a href="{{ route('gallery') }}"  class="hover:text-brand-yellow transition-colors">{{ __('Gallery') }}</a></li>
                        <li><a href="{{ route('booking') }}"  class="hover:text-brand-yellow transition-colors">{{ __('Book Appointment') }}</a></li>
                        <li><a href="{{ route('faq') }}"      class="hover:text-brand-yellow transition-colors">{{ __('FAQ') }}</a></li>
                        <li><a href="{{ route('about') }}"    class="hover:text-brand-yellow transition-colors">{{ __('About Us') }}</a></li>
                        <li><a href="{{ route('contact') }}"  class="hover:text-brand-yellow transition-colors">{{ __('Contact') }}</a></li>
                        <li><a href="{{ route('privacy-policy') }}" class="hover:text-brand-yellow transition-colors">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="{{ route('terms-of-service') }}" class="hover:text-brand-yellow transition-colors">{{ __('Terms of Service') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-white mb-4 uppercase text-sm tracking-wider">{{ __('Contact Us') }}</h4>
                    <address class="not-italic space-y-2 text-sm">
                        <div class="flex items-start gap-2">
                            <span class="text-brand-yellow mt-0.5 flex-shrink-0" aria-hidden="true">📍</span>
                            <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-brand-yellow transition-colors">{{ $storeAddress }}</a>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-brand-yellow flex-shrink-0" aria-hidden="true">📞</span>
                            <a href="{{ $telLink }}" class="hover:text-brand-yellow transition-colors">{{ $storePhoneDisplay }}</a>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-brand-yellow flex-shrink-0" aria-hidden="true">✉</span>
                            <a href="mailto:{{ $storeEmail }}" class="hover:text-brand-yellow transition-colors break-all">{{ $storeEmail }}</a>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-brand-yellow flex-shrink-0" aria-hidden="true">💬</span>
                            <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-brand-yellow transition-colors">{{ __('WhatsApp us') }}</a>
                        </div>
                        @if($storeFacebookUrl)
                        <div class="flex items-center gap-2">
                            <span class="text-brand-yellow flex-shrink-0" aria-hidden="true">f</span>
                            <a href="{{ $storeFacebookUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-brand-yellow transition-colors">Facebook</a>
                        </div>
                        @endif
                        @if($storeHours)
                        <div class="flex items-center gap-2">
                            <span class="text-brand-yellow flex-shrink-0" aria-hidden="true">🕐</span>
                            <span>{{ $storeHours }}</span>
                        </div>
                        @endif
                    </address>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500 gap-2">
                <p>&copy; {{ date('Y') }} {{ $storeName }}. {{ __('All rights reserved.') }}</p>
                <p>{{ __('Visit the showroom or chat with us on WhatsApp for product advice.') }}</p>
            </div>
        </div>
    </footer>

    <script>
        const themeBtn = document.getElementById('theme-btn');
        const themeMenu = document.getElementById('theme-menu');
        const themeWrapper = document.getElementById('theme-wrapper');
        const iconDisplay = document.getElementById('icon-theme-display');

        const icons = {
            light: '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>',
            dark: '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>',
            system: '<rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>'
        };

        function applyTheme(theme) {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (theme === 'system') {
                document.documentElement.classList.toggle('dark', prefersDark);
            } else {
                document.documentElement.classList.toggle('dark', theme === 'dark');
            }
            iconDisplay.innerHTML = icons[theme];
            localStorage.setItem('theme', theme);
        }

        const savedTheme = localStorage.getItem('theme') || 'system';
        applyTheme(savedTheme);

        themeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            themeMenu.classList.toggle('hidden');
        });

        document.querySelectorAll('.theme-option').forEach(btn => {
            btn.addEventListener('click', (e) => {
                applyTheme(e.currentTarget.dataset.theme);
                themeMenu.classList.add('hidden');
            });
        });

        document.addEventListener('click', (e) => {
            if (!themeWrapper.contains(e.target)) {
                themeMenu.classList.add('hidden');
            }
        });

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (localStorage.getItem('theme') === 'system') applyTheme('system');
        });

        const langBtn = document.getElementById('lang-btn');
        const langMenu = document.getElementById('lang-menu');
        const langWrapper = document.getElementById('lang-wrapper');

        function openLang() {
            langMenu.classList.remove('hidden');
            langBtn.setAttribute('aria-expanded', 'true');
        }

        function closeLang() {
            langMenu.classList.add('hidden');
            langBtn.setAttribute('aria-expanded', 'false');
        }

        langBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            langMenu.classList.contains('hidden') ? openLang() : closeLang();
        });

        document.addEventListener('click', function (e) {
            if (!langWrapper.contains(e.target)) {
                closeLang();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeLang();
            }
        });

        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const iconHamburger = document.getElementById('icon-hamburger');
        const iconClose = document.getElementById('icon-close');

        mobileBtn.addEventListener('click', function () {
            const opening = mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            iconHamburger.classList.toggle('hidden', opening);
            iconClose.classList.toggle('hidden', !opening);
            this.setAttribute('aria-expanded', opening ? 'true' : 'false');
        });
    </script>
    
    @livewire('ai-chatbot')

    @livewireScripts

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        // ── AOS init ─────────────────────────────────────────
        AOS.init({
            duration: 650,
            easing: 'ease-out-cubic',
            once: true,
            offset: 60,
            delay: 0,
        });

        // Re-init after Livewire navigations keep animations fresh
        document.addEventListener('livewire:navigated', () => AOS.refresh());

        // ── Animated counter ─────────────────────────────────
        // Usage: <span data-count="500" data-suffix="+">500+</span>
        function animateCounters(root) {
            const els = (root || document).querySelectorAll('[data-count]');
            if (!els.length) return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    observer.unobserve(entry.target);

                    const el     = entry.target;
                    const target = parseInt(el.dataset.count, 10);
                    const suffix = el.dataset.suffix || '';
                    const prefix = el.dataset.prefix || '';
                    const dur    = 1400;
                    const start  = performance.now();

                    function tick(now) {
                        const progress = Math.min((now - start) / dur, 1);
                        // ease-out-expo
                        const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                        const val  = Math.floor(ease * target);
                        el.textContent = prefix + val.toLocaleString() + suffix;
                        if (progress < 1) requestAnimationFrame(tick);
                    }
                    requestAnimationFrame(tick);
                });
            }, { threshold: 0.4 });

            els.forEach(el => observer.observe(el));
        }

        animateCounters();

        // Re-run after Livewire re-renders
        document.addEventListener('livewire:navigated', () => animateCounters());
    </script>
</body>
</html>
