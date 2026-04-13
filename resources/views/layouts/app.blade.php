<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
@php
    use Artesaos\SEOTools\Facades\SEOMeta;
    use Artesaos\SEOTools\Facades\OpenGraph;
    use Artesaos\SEOTools\Facades\Twitter;
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
    <meta name="theme-color" content="#DC2626" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#111827" media="(prefers-color-scheme: dark)">
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
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
                            red: '#DC2626',
                            yellow: '#FBBF24',
                            black: '#111827',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .skip-link {
            position: absolute;
            top: -48px;
            left: 8px;
            background: #DC2626;
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
            outline: 3px solid #DC2626;
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
            background: linear-gradient(135deg, #111827 0%, #1f2937 50%, #DC2626 100%);
        }

        body {
            transition: background-color 0.2s, color 0.2s;
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

    <div class="bg-brand-black text-xs text-gray-400 py-1.5" role="banner">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row justify-between items-center gap-2">
            <span class="hidden sm:inline">{{ __('Product showcase, showroom visits, and WhatsApp consultation') }}</span>
            <a href="{{ $whatsAppUrl }}"
               target="_blank"
               rel="noopener noreferrer"
               class="group flex items-center gap-1.5 text-gray-300 hover:text-[#25D366] transition-colors duration-300"
               aria-label="{{ __('WhatsApp us') }}">
                <!-- YesIcon / svgl Premium WhatsApp Logo -->
                <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-300" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span class="font-bold relative overflow-hidden group-hover:text-[#25D366]">
                    WhatsApp: {{ $storePhoneDisplay }}
                </span>
            </a>
        </div>
    </div>

    <nav class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50 border-b border-gray-100 dark:border-gray-700"
         role="navigation"
         aria-label="{{ __('Main navigation') }}">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
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

                <!-- Desktop Nav -->
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

                <!-- Auth & Action Buttons -->
                <div class="hidden md:flex items-center gap-2 lg:gap-3">
                    <a href="{{ $whatsAppUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="hidden lg:inline-flex bg-brand-red text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-red-700 transition-colors whitespace-nowrap">
                        {{ __('WhatsApp us') }}
                    </a>
                    @auth
                        <span class="hidden lg:inline text-sm text-gray-600 dark:text-gray-300">Hi, <strong class="text-brand-black dark:text-white">{{ Auth::user()->name }}</strong></span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1.5 lg:px-4 lg:py-2 rounded-full text-xs lg:text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition whitespace-nowrap">
                                {{ __('Logout') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-xs lg:text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-brand-red transition whitespace-nowrap">
                            {{ __('Sign In') }}
                        </a>
                        <a href="{{ route('login') }}" class="bg-gray-800 dark:bg-gray-600 text-white px-3 py-1.5 lg:px-5 lg:py-2 rounded-full text-xs lg:text-sm font-semibold hover:bg-gray-900 dark:hover:bg-gray-500 transition whitespace-nowrap">
                            {{ __('Register') }}
                        </a>
                    @endauth
                </div>

                <div class="flex items-center gap-1.5">

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

                    <button id="mobile-menu-btn"
                            class="md:hidden p-2 rounded-lg transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
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
                    <a href="{{ $whatsAppUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="block py-2.5 px-3 bg-brand-red text-white rounded-lg font-semibold text-center hover:bg-red-700 transition-colors">
                        {{ __('WhatsApp us') }}
                    </a>
                </div>
                <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                    @auth
                        <span class="block py-2 px-3 text-sm text-gray-500 dark:text-gray-400">{{ __('Signed in as') }} <strong class="dark:text-white">{{ Auth::user()->name }}</strong></span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left py-2.5 px-3 rounded-lg font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">{{ __('Logout') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block py-2.5 px-3 rounded-lg font-medium text-brand-red hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">{{ __('Sign In / Register') }}</a>
                    @endauth
                </div>
            </div>
        </div>
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
                        <li><a href="{{ route('about') }}"    class="hover:text-brand-yellow transition-colors">{{ __('About Us') }}</a></li>
                        <li><a href="{{ route('contact') }}"  class="hover:text-brand-yellow transition-colors">{{ __('Contact') }}</a></li>
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
