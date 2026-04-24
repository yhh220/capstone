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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                fontFamily: {
                    sans: ['"DM Sans"', 'sans-serif'],
                    display: ['"Anton"', 'sans-serif'],
                },
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
        h1, h2, h3, h4, h5, h6, .font-display {
            font-family: 'Anton', sans-serif !important;
            font-weight: 400 !important;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        /* ── Ember Carbon color theme ──────────────────────────
           Brand    : Ember Red #C8413D · Carbon Black #121212 · Asphalt #1C1917
           Neutrals : Bone White #E8E0D8 · Chalk #F7F5F3 · Warm Ash #8C8480
           Borders  : Gunmetal #3A3330 · Deep Slate #2E2A28
           Accents  : Ember Dark #A83432 (hover) · Ember Light #E86460 (glow)
        ──────────────────────────────────────────────────────── */
        :root {
            --brand-red-rgb: 200 65 61;       /* #C8413D — Ember Red (CTA, focus) */
            --brand-yellow-rgb: 232 100 96;   /* #E86460 — Ember Light (accent/glow) */
            --brand-black-rgb: 18 18 18;      /* #121212 — Carbon Black */
            --brand-red-hover-rgb: 168 52 50; /* #A83432 — Ember Dark (hover) */
        }
        .dark {
            --brand-red-rgb: 232 100 96;      /* #E86460 — Ember Light on dark */
            --brand-yellow-rgb: 232 224 216;  /* #E8E0D8 — Bone White accent */
            --brand-black-rgb: 28 25 23;      /* #1C1917 — Asphalt surface */
            --brand-red-hover-rgb: 200 65 61; /* #C8413D — Ember Red hover on dark */
        }

        /* Carbon-black body in dark mode */
        .dark body {
            background-color: #121212;
            color: #E8E0D8;
        }

        :root {
            --app-bg-rgb: 247 245 243;        /* #F7F5F3 — Chalk */
            --app-surface-rgb: 255 255 255;
            --app-surface-soft-rgb: 232 224 216; /* #E8E0D8 — Bone White */
            --app-border-rgb: 224 218 210;    /* Bone White tint */
            --app-text-rgb: 18 18 18;         /* #121212 — Carbon Black */
            --app-muted-rgb: 140 132 128;     /* #8C8480 — Warm Ash */
        }

        .dark {
            --app-bg-rgb: 18 18 18;           /* #121212 — Carbon Black */
            --app-surface-rgb: 28 25 23;      /* #1C1917 — Asphalt */
            --app-surface-soft-rgb: 46 42 40; /* #2E2A28 — Deep Slate */
            --app-border-rgb: 58 51 48;       /* #3A3330 — Gunmetal */
            --app-text-rgb: 232 224 216;      /* #E8E0D8 — Bone White */
            --app-muted-rgb: 140 132 128;     /* #8C8480 — Warm Ash */
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
            border-color: rgb(var(--app-border-rgb));
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
            color: rgb(140 132 128) !important; /* Warm Ash */
        }

        .dark .dark\:hover\:bg-gray-700:hover,
        .dark .dark\:hover\:bg-gray-700\/50:hover {
            background-color: rgb(46 42 40) !important; /* Deep Slate */
        }

        .dark .dark\:bg-red-900\/10,
        .dark .dark\:bg-red-900\/20,
        .dark .dark\:bg-red-900\/30,
        .dark .dark\:hover\:bg-red-900\/20:hover {
            background-color: rgb(168 52 50 / 0.22) !important; /* Ember Dark tint */
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
            background: linear-gradient(135deg, #121212 0%, #2E2A28 50%, rgb(var(--brand-red-rgb)) 100%);
        }
        .dark .hero-gradient {
            background: linear-gradient(135deg, #121212 0%, #1C1917 50%, rgb(var(--brand-red-rgb)) 100%);
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

        /* ── Scroll-reveal system ────────────────────────── */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1),
                        transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .scroll-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .scroll-reveal-left {
            opacity: 0;
            transform: translateX(-40px);
            transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1),
                        transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .scroll-reveal-left.is-visible {
            opacity: 1;
            transform: translateX(0);
        }
        .scroll-reveal-right {
            opacity: 0;
            transform: translateX(40px);
            transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1),
                        transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .scroll-reveal-right.is-visible {
            opacity: 1;
            transform: translateX(0);
        }
        .stagger-1 { transition-delay: 0.00s; }
        .stagger-2 { transition-delay: 0.12s; }
        .stagger-3 { transition-delay: 0.24s; }
        .stagger-4 { transition-delay: 0.36s; }
        .stagger-5 { transition-delay: 0.48s; }
        .stagger-6 { transition-delay: 0.60s; }

        /* ── Language menu entrance ──────────────────── */
        @keyframes langMenuIn {
            from { opacity: 0; transform: translateY(-8px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0)   scale(1);    }
        }
        .lang-menu-enter { animation: langMenuIn 0.2s cubic-bezier(0.22, 1, 0.36, 1) both; }

        /* ── Theme segmented pill active state ───────── */
        .theme-seg-active {
            background: white;
            box-shadow: 0 1px 4px rgba(0,0,0,0.12), 0 0 0 1px rgba(0,0,0,0.05);
            color: #E11D48 !important;
        }
        .dark .theme-seg-active {
            background: rgb(55, 65, 81);
            box-shadow: 0 1px 4px rgba(0,0,0,0.35);
            color: #E11D48 !important;
        }
    </style>

    @livewireStyles
    @stack('styles')
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
            <div class="flex items-center justify-between h-14 sm:h-20 gap-1.5 sm:gap-2">
                <!-- Brand Identity -->
                <a href="{{ route('home') }}"
                   class="flex flex-col items-center justify-center flex-shrink-0 group leading-none py-1"
                   aria-label="{{ $storeName }} - {{ __('Home') }}">
                    <div class="h-8 w-24 sm:h-10 sm:w-32 flex items-center justify-center flex-shrink-0 transition-transform duration-300 group-hover:scale-105 group-active:scale-95" aria-hidden="true">
                        <img src="{{ asset('images/logo/logo-dark.svg') }}" alt="" class="h-full w-full object-contain block dark:hidden">
                        <img src="{{ asset('images/logo/logo-light.svg') }}" alt="" class="h-full w-full object-contain hidden dark:block">
                    </div>
                    <div class="mt-0.5 sm:mt-1 hidden sm:flex items-center gap-1.5 leading-none transition-opacity duration-300 group-hover:opacity-80">
                        <span class="font-black text-brand-black dark:text-white text-[11px] uppercase tracking-[0.15em]">{{ $storeShortName }}</span>
                        <span class="text-[10px] text-brand-red font-semibold uppercase tracking-widest">{{ $storeTagline }}</span>
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
                       class="relative px-2.5 py-1.5 lg:px-4 lg:py-2 rounded-full text-xs lg:text-sm font-bold transition-all duration-300 ease-out whitespace-nowrap overflow-hidden
                              {{ $active ? 'text-brand-red bg-red-50 dark:bg-red-900/10' : 'text-gray-600 dark:text-gray-300 hover:text-brand-red after:content-[\'\'] after:absolute after:bottom-1 after:left-1/2 after:-translate-x-1/2 after:w-0 hover:after:w-3/4 after:h-[2px] after:bg-brand-red after:transition-all after:duration-300' }}"
                       @if($active) aria-current="page" @endif>
                        {{ $label }}
                    </a>
                    @endforeach
                </div>

                <!-- Right-side icons: Lang, Theme, Cart (cond), User, WhatsApp, Mobile menu -->
                <div class="flex items-center gap-1 sm:gap-1.5">

                    <!-- 1. Language switcher -->
                    <div class="relative" id="lang-wrapper">
                        <button id="lang-btn"
                                aria-label="{{ __('Select language') }}"
                                aria-expanded="false"
                                aria-haspopup="true"
                                class="group flex items-center gap-1 sm:gap-1.5 px-2 py-1.5 sm:px-2.5 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-300 hover:text-brand-red bg-gray-50/80 dark:bg-gray-800/80 hover:bg-red-50/80 dark:hover:bg-red-900/20 border border-gray-200/60 dark:border-gray-700/60 hover:border-brand-red/40 transition-all duration-300 hover:shadow-sm backdrop-blur-sm">
                            <svg class="w-3.5 h-3.5 opacity-50 group-hover:opacity-90 transition-opacity duration-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                            </svg>
                            <span class="tracking-wide">
                                @if(app()->getLocale() === 'ms') BM
                                @elseif(app()->getLocale() === 'zh') 中文
                                @else EN
                                @endif
                            </span>
                            <svg class="w-3 h-3 opacity-50 group-hover:opacity-90 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>

                        <div id="lang-menu"
                             role="menu"
                             aria-orientation="vertical"
                             class="hidden absolute right-0 top-full mt-2 w-52 rounded-2xl shadow-2xl border border-gray-100/80 dark:border-gray-700/50 overflow-hidden z-50 backdrop-blur-xl bg-white/95 dark:bg-gray-900/95 lang-menu-enter">
                            <div class="px-4 pt-3 pb-2">
                                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.15em]">{{ __('Language') }}</span>
                            </div>
                            <div class="px-2 pb-2 space-y-0.5">
                                @foreach([
                                    ['en', 'English',       'EN'],
                                    ['ms', 'Bahasa Melayu', 'BM'],
                                    ['zh', '中文',           'ZH'],
                                ] as [$code, $name, $short])
                                <button type="button"
                                   data-lang="{{ $code }}"
                                   data-lang-url="{{ route('lang', $code) }}"
                                   role="menuitem"
                                   class="w-full text-left flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ app()->getLocale() === $code ? 'bg-red-50 dark:bg-red-900/20 text-brand-red' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800/80' }}">
                                    <span class="flex-1 text-sm font-semibold">{{ $name }}</span>
                                    <svg class="lang-check w-4 h-4 text-brand-red shrink-0 {{ app()->getLocale() !== $code ? 'hidden' : '' }}" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 2. Dark mode toggle (segmented pill) — hidden on xs, visible sm+ -->
                    <div id="theme-wrapper" class="hidden sm:flex items-center p-1 rounded-xl bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/60 dark:border-gray-700/60 backdrop-blur-sm gap-0.5">
                        <button class="theme-option p-1.5 sm:p-2 rounded-lg transition-all duration-200 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300" data-theme="light" title="Light" aria-label="{{ __('Light mode') }}">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
                            </svg>
                        </button>
                        <button class="theme-option p-1.5 sm:p-2 rounded-lg transition-all duration-200 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300" data-theme="dark" title="Dark" aria-label="{{ __('Dark mode') }}">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                            </svg>
                        </button>
                        <button class="theme-option p-1.5 sm:p-2 rounded-lg transition-all duration-200 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300" data-theme="system" title="System" aria-label="{{ __('System theme') }}">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/>
                            </svg>
                        </button>
                    </div>

                    <!-- 3. Cart icon (only when shopping enabled) -->
                    @if($shoppingEnabled)
                    <button type="button"
                            @click="cartOpen = true"
                            aria-label="{{ __('Open cart') }}"
                            class="group relative p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-brand-red hover:text-white border border-transparent hover:border-brand-red transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                        <svg class="w-4 h-4 transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-[10deg]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                        </svg>
                        @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 bg-brand-red text-white text-[10px] leading-none font-bold rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center transform transition-transform duration-300 group-hover:scale-110 group-hover:bg-white group-hover:text-brand-red shadow-sm">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                        @endif
                    </button>
                    @endif

                    <!-- 4. User dropdown (desktop) -->
                    @if($shoppingEnabled || (auth()->check() && !auth()->user()->isAdmin()))
                    <div class="hidden md:block relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                @click.outside="open = false"
                                type="button"
                                class="group flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition-all duration-300 hover:shadow-sm"
                                aria-label="{{ __('User menu') }}">
                            @auth
                                <div class="w-6 h-6 rounded-full bg-brand-red flex items-center justify-center text-white text-[11px] font-black group-hover:scale-110 transition-transform duration-300">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @else
                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300 transition-transform duration-300 group-hover:scale-110 group-hover:-translate-y-0.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
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
                                @if($shoppingEnabled)
                                <div class="py-1">
                                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        {{ __('Sign In') }}
                                    </a>
                                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                        {{ __('Register') }}
                                    </a>
                                </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                    @endif

                    <!-- 5. WhatsApp button (desktop only) -->
                    <a href="{{ $whatsAppUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="hidden md:flex items-center justify-center p-2 rounded-lg bg-[#25D366] text-white hover:bg-[#1EBE57] hover:scale-110 active:scale-95 transition-all duration-300 shadow-md hover:shadow-lg group"
                       aria-label="{{ __('WhatsApp us') }}">
                        <!-- Premium WhatsApp SVGL Icon -->
                        <svg class="w-5 h-5 drop-shadow-sm group-hover:rotate-[15deg] transition-transform duration-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>

                    <!-- 6. Mobile menu button (Lucide Animated) -->
                    <button id="mobile-menu-btn"
                            class="md:hidden group p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition-all duration-300"
                            aria-label="{{ __('Toggle mobile menu') }}"
                            aria-expanded="false"
                            aria-controls="mobile-menu">
                        <svg id="icon-hamburger" class="w-5 h-5 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                            <line x1="4" x2="20" y1="12" y2="12"></line><line x1="4" x2="20" y1="6" y2="6"></line><line x1="4" x2="20" y1="18" y2="18"></line>
                        </svg>
                        <svg id="icon-close" class="w-5 h-5 hidden transition-transform duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                            <line x1="18" x2="6" y1="6" y2="18"></line><line x1="6" x2="18" y1="6" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu"
                 class="hidden md:hidden pb-4 space-y-1 border-t border-gray-100 dark:border-gray-700 pt-3"
                 role="menu">

                <!-- Theme toggle inside mobile menu (xs screens) -->
                <div class="sm:hidden flex items-center gap-2 px-3 py-2 mb-1">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 mr-1">{{ __('Theme') }}:</span>
                    <div class="flex items-center p-1 rounded-xl bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/60 dark:border-gray-700/60 gap-0.5">
                        <button class="theme-option p-1.5 rounded-lg transition-all duration-200 text-gray-400 dark:text-gray-500 hover:text-gray-600" data-theme="light" title="Light" aria-label="{{ __('Light mode') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                        </button>
                        <button class="theme-option p-1.5 rounded-lg transition-all duration-200 text-gray-400 dark:text-gray-500 hover:text-gray-600" data-theme="dark" title="Dark" aria-label="{{ __('Dark mode') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                        </button>
                        <button class="theme-option p-1.5 rounded-lg transition-all duration-200 text-gray-400 dark:text-gray-500 hover:text-gray-600" data-theme="system" title="System" aria-label="{{ __('System theme') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/></svg>
                        </button>
                    </div>
                </div>
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

                @if($shoppingEnabled || (auth()->check() && !auth()->user()->isAdmin()))
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
                        @if($shoppingEnabled)
                        <a href="{{ route('login') }}" class="block py-2.5 px-3 rounded-lg font-medium text-brand-red hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">{{ __('Sign In') }}</a>
                        <a href="{{ route('login') }}" class="block py-2.5 px-3 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">{{ __('Register') }}</a>
                        @endif
                    @endauth
                </div>
                @endif
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
                    <div class="mb-4">
                        <div class="h-14 w-44 flex items-center justify-start flex-shrink-0 mb-2" aria-hidden="true">
                            <img src="{{ asset('images/logo/logo-light.svg') }}" alt="" class="h-full w-full object-contain">
                        </div>
                        <div class="font-black text-white text-lg leading-tight">{{ $storeName }}</div>
                        <div class="text-xs text-brand-yellow uppercase tracking-widest mt-0.5">{{ $storeTagline }}</div>
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
                        <li><a href="{{ route('booking') }}"  class="hover:text-brand-yellow transition-colors">{{ __('Book Appointment') }}</a></li>
                        <li><a href="{{ route('about') }}"    class="hover:text-brand-yellow transition-colors">{{ __('About Us') }}</a></li>
                        <li><a href="{{ route('contact') }}"  class="hover:text-brand-yellow transition-colors">{{ __('Contact') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-white mb-4 uppercase text-sm tracking-wider">{{ __('Contact Us') }}</h4>
                    <address class="not-italic space-y-2 text-sm">
                        <div class="flex items-start gap-2 group">
                            <!-- Premium Map Pin -->
                            <svg class="w-4 h-4 text-brand-yellow mt-0.5 flex-shrink-0 group-hover:-translate-y-1 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-brand-yellow transition-colors">{{ $storeAddress }}</a>
                        </div>
                        <div class="flex items-center gap-2 group">
                            <!-- Premium Phone -->
                            <svg class="w-4 h-4 text-brand-yellow flex-shrink-0 group-hover:-translate-y-1 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <a href="{{ $telLink }}" class="hover:text-brand-yellow transition-colors">{{ $storePhoneDisplay }}</a>
                        </div>
                        <div class="flex items-center gap-2 group">
                            <!-- Premium Mail -->
                            <svg class="w-4 h-4 text-brand-yellow flex-shrink-0 group-hover:-translate-y-1 group-hover:-rotate-6 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                            <a href="mailto:{{ $storeEmail }}" class="hover:text-brand-yellow transition-colors break-all">{{ $storeEmail }}</a>
                        </div>
                        <div class="flex items-center gap-2 group">
                            <!-- Premium WhatsApp Outline -->
                            <svg class="w-4 h-4 text-[#25D366] flex-shrink-0 group-hover:-translate-y-1 group-hover:scale-110 group-hover:rotate-[15deg] transition-transform duration-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-brand-yellow transition-colors">{{ __('WhatsApp us') }}</a>
                        </div>
                        @if($storeFacebookUrl)
                        <div class="flex items-center gap-2 group">
                            <!-- Premium Facebook SVGL outline icon -->
                            <svg class="w-4 h-4 text-[#1877F2] group-hover:-translate-y-1 group-hover:scale-110 transition-transform duration-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            <a href="{{ $storeFacebookUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-brand-yellow transition-colors group-hover:tracking-wider duration-300">Facebook</a>
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

            <div class="border-t border-gray-700 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500 gap-3">
                <p>&copy; {{ $storeName }} 2025. <span>{{ __('All rights reserved.') }}</span></p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('privacy-policy') }}" class="hover:text-brand-yellow transition-colors">{{ __('Privacy Policy') }}</a>
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('terms-of-service') }}" class="hover:text-brand-yellow transition-colors">{{ __('Terms of Service') }}</a>
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('faq') }}" class="hover:text-brand-yellow transition-colors">{{ __('FAQ') }}</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function updateThemeSegment(theme) {
            document.querySelectorAll('.theme-option').forEach(btn => {
                btn.classList.toggle('theme-seg-active', btn.dataset.theme === theme);
            });
        }

        function applyTheme(theme) {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', theme === 'system' ? prefersDark : theme === 'dark');
            localStorage.setItem('theme', theme);
            updateThemeSegment(theme);
        }

        const savedTheme = localStorage.getItem('theme') || 'system';
        applyTheme(savedTheme);

        document.querySelectorAll('.theme-option').forEach(btn => {
            btn.addEventListener('click', e => applyTheme(e.currentTarget.dataset.theme));
        });

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (localStorage.getItem('theme') === 'system') applyTheme('system');
        });

        const langBtn = document.getElementById('lang-btn');
        const langMenu = document.getElementById('lang-menu');
        const langWrapper = document.getElementById('lang-wrapper');

        function openLang() {
            langMenu.classList.remove('hidden');
            langMenu.classList.remove('lang-menu-enter');
            void langMenu.offsetWidth; // reflow to restart animation
            langMenu.classList.add('lang-menu-enter');
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

        // Instant client-side language switching — zero reload
        (function () {
            var translations = {
                ms: @json(json_decode(file_get_contents(base_path('lang/ms.json')), true)),
                zh: @json(json_decode(file_get_contents(base_path('lang/zh.json')), true))
            };
            var currentLocale = '{{ app()->getLocale() }}';

            // Build reverse map: displayed text → translation key
            function buildReverseMap(locale) {
                var map = {};
                if (locale === 'en') {
                    Object.keys(translations.ms).forEach(function (k) { map[k] = k; });
                } else if (translations[locale]) {
                    Object.entries(translations[locale]).forEach(function ([k, v]) { map[v] = k; });
                }
                return map;
            }

            // Translatable HTML attributes (placeholders, aria-labels, titles)
            var TRANSLATABLE_ATTRS = ['placeholder', 'aria-label', 'title'];

            // Swap translatable attributes on all elements
            function swapAttrs(reverseMap, target) {
                TRANSLATABLE_ATTRS.forEach(function (attr) {
                    document.querySelectorAll('[' + attr + ']').forEach(function (el) {
                        var val = (el.getAttribute(attr) || '').trim();
                        if (!val) return;
                        var key = reverseMap[val];
                        if (!key) return;
                        el.setAttribute(attr, target ? (target[key] || key) : key);
                    });
                });
            }

            // Swap all text nodes in the DOM
            function swapLocale(toLocale) {
                if (toLocale === currentLocale) return;
                var reverseMap = buildReverseMap(currentLocale);
                var target = toLocale === 'en' ? null : translations[toLocale];

                // 1. Text nodes
                var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
                var nodes = [];
                var node;
                while ((node = walker.nextNode())) nodes.push(node);

                nodes.forEach(function (n) {
                    var trimmed = n.nodeValue.trim();
                    if (!trimmed) return;
                    var key = reverseMap[trimmed];
                    if (!key) return;
                    var newText = target ? (target[key] || key) : key;
                    n.nodeValue = n.nodeValue.replace(trimmed, newText);
                });

                // 2. Translatable attributes (placeholder, aria-label, title)
                swapAttrs(reverseMap, target);

                // Update lang button label
                var labels = { en: 'EN', ms: 'BM', zh: '中文' };
                var langBtnSpan = langBtn.querySelector('span');
                if (langBtnSpan) langBtnSpan.textContent = labels[toLocale];

                // Update active state of menu items
                document.querySelectorAll('[data-lang]').forEach(function (btn) {
                    var isActive = btn.dataset.lang === toLocale;
                    btn.classList.toggle('bg-red-50', isActive);
                    btn.classList.toggle('text-brand-red', isActive);
                    btn.classList.toggle('text-gray-700', !isActive);
                    btn.classList.toggle('dark:text-gray-300', !isActive);
                    var check = btn.querySelector('.lang-check');
                    if (check) check.classList.toggle('hidden', !isActive);
                });

                // Update html lang attribute
                document.documentElement.lang = toLocale;
                currentLocale = toLocale;

                // Persist locale to session silently
                fetch('/lang/' + toLocale, { redirect: 'follow' });
                closeLang();
            }

            document.querySelectorAll('[data-lang]').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    swapLocale(this.dataset.lang);
                });
            });

            // ── Fix Livewire race condition ─────────────────────────────
            // When Livewire re-renders a component, the server may respond in
            // a different locale than the client's current locale (because the
            // session-locale fetch is async). This re-applies the correct locale
            // to any newly injected text nodes/attributes after every Livewire update.
            function reapplyCurrentLocale() {
                if (currentLocale === 'en') {
                    // EN mode: reverse any foreign-language text/attrs that slipped in
                    ['zh', 'ms'].forEach(function (loc) {
                        if (!translations[loc]) return;
                        var revMap = {};
                        Object.entries(translations[loc]).forEach(function (entry) {
                            revMap[entry[1]] = entry[0];
                        });
                        // Text nodes
                        var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
                        var nodes = [];
                        var node;
                        while ((node = walker.nextNode())) nodes.push(node);
                        nodes.forEach(function (n) {
                            var trimmed = n.nodeValue.trim();
                            if (!trimmed) return;
                            var key = revMap[trimmed];
                            if (!key) return;
                            n.nodeValue = n.nodeValue.replace(trimmed, key);
                        });
                        // Attributes
                        swapAttrs(revMap, null);
                    });
                } else {
                    // Non-EN mode: translate any English nodes/attrs Livewire just injected.
                    // Temporarily pretend we're in EN — already-translated nodes are not
                    // in the EN→EN reverse map so they get skipped automatically.
                    var savedLocale = currentLocale;
                    currentLocale = 'en';
                    swapLocale(savedLocale);
                }
            }

            document.addEventListener('livewire:updated', reapplyCurrentLocale);
            document.addEventListener('livewire:navigated', reapplyCurrentLocale);
        }());

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
    @stack('scripts')

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
    <!-- Scroll to Top Button -->
    <button id="scroll-to-top"
            aria-label="{{ __('Scroll to top') }}"
            class="fixed bottom-6 right-6 sm:bottom-8 sm:left-1/2 sm:-translate-x-1/2 z-40 w-10 h-10 flex items-center justify-center rounded-full bg-brand-red text-white shadow-2xl transition-all duration-500 translate-y-20 opacity-0 pointer-events-none hover:bg-brand-red/90 hover:-translate-y-1 active:scale-95 focus:outline-none focus:ring-4 focus:ring-brand-red/30 group">
        <svg class="w-5 h-5 transition-transform duration-300 group-hover:-translate-y-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
        </svg>
    </button>

    <script>
        // ── Scroll to Top Logic ──────────────────────────────
        (function() {
            const btn = document.getElementById('scroll-to-top');
            if (!btn) return;

            function toggleBtn() {
                if (window.scrollY > 400) {
                    btn.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
                } else {
                    btn.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
                }
            }

            window.addEventListener('scroll', toggleBtn);
            btn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            
            // Re-check on navigation
            document.addEventListener('livewire:navigated', toggleBtn);
        })();
    </script>
</body>
</html>
