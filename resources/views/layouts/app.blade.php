<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Win Win Car Studio Accessories — Malaysia's premier car accessories store. Premium seat covers, audio systems, dash cams, LED lights and more.">
    <meta name="theme-color" content="#DC2626" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#111827" media="(prefers-color-scheme: dark)">
    <meta name="robots" content="index, follow">
    <title>@yield('title', 'Win Win Car Studio Accessories')</title>

    {{-- Dark mode: run BEFORE any CSS renders to prevent flash of wrong theme --}}
    <script>
        (function () {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

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
                    },
                    fontSize: {
                        base: ['1rem', { lineHeight: '1.6' }],
                    }
                }
            }
        }
    </script>

    <style>
        /* ── Accessibility: skip-nav link ── */
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
        .skip-link:focus { top: 0; }

        /* ── Keyboard focus ring ── */
        *:focus-visible {
            outline: 3px solid #DC2626;
            outline-offset: 2px;
            border-radius: 4px;
        }

        /* ── Respect prefers-reduced-motion ── */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        [x-cloak] { display: none !important; }

        /* Hero gradient shared across pages */
        .hero-gradient {
            background: linear-gradient(135deg, #111827 0%, #1f2937 50%, #DC2626 100%);
        }

        /* Smooth dark-mode body transition */
        body { transition: background-color 0.2s, color 0.2s; }
    </style>

    @livewireStyles
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans antialiased">

    {{-- Skip to main content (screen-reader + keyboard users) --}}
    <a href="#main-content" class="skip-link">{{ __('Skip to main content') }}</a>

    {{-- ── Top announcement bar ── --}}
    <div class="bg-brand-black text-xs text-gray-400 py-1.5" role="banner">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row justify-between items-center gap-1">
            <span>{{ __('Free shipping on orders above RM150') }}</span>
            <a href="tel:+60123456789"
               class="hover:text-brand-yellow transition-colors"
               aria-label="{{ __('Call us') }}: +60 12-345 6789">
                📞 +60 12-345 6789
            </a>
        </div>
    </div>

    {{-- ── Main navigation ── --}}
    <nav class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50 border-b border-gray-100 dark:border-gray-700"
         role="navigation"
         aria-label="{{ __('Main navigation') }}">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}"
                   class="flex items-center gap-2 flex-shrink-0"
                   aria-label="Win Win Car Studio – {{ __('Home') }}">
                    <div class="w-10 h-10 bg-brand-red rounded-full flex items-center justify-center flex-shrink-0" aria-hidden="true">
                        <span class="text-brand-yellow font-black text-lg">W</span>
                    </div>
                    <div class="leading-tight">
                        <div class="font-black text-brand-black dark:text-white text-sm uppercase tracking-wide">Win Win</div>
                        <div class="text-xs text-brand-red font-semibold uppercase tracking-widest">Car Studio</div>
                    </div>
                </a>

                {{-- Desktop nav links --}}
                <div class="hidden md:flex items-center gap-6" role="list">
                    @foreach([
                        [route('home'),     __('Home'),     request()->routeIs('home')],
                        [route('products'), __('Products'), request()->routeIs('products*')],
                        [route('about'),    __('About Us'), request()->routeIs('about')],
                        [route('contact'),  __('Contact'),  request()->routeIs('contact')],
                    ] as [$href, $label, $active])
                    <a href="{{ $href }}"
                       role="listitem"
                       class="font-medium text-sm transition-colors duration-200
                              {{ $active
                                 ? 'text-brand-red font-bold'
                                 : 'text-gray-700 dark:text-gray-300 hover:text-brand-red dark:hover:text-brand-yellow' }}"
                       @if($active) aria-current="page" @endif>
                        {{ $label }}
                    </a>
                    @endforeach
                </div>

                {{-- Right side controls --}}
                <div class="flex items-center gap-1.5">

                    {{-- Shop Now CTA (desktop only) --}}
                    <a href="{{ route('products') }}"
                       class="hidden md:inline-block bg-brand-red text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-red-700 transition-colors mr-1">
                        {{ __('Shop Now') }}
                    </a>

                    {{-- ── Language switcher ── --}}
                    <div class="relative" id="lang-wrapper">
                        <button id="lang-btn"
                                aria-label="{{ __('Select language') }}"
                                aria-expanded="false"
                                aria-haspopup="true"
                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold
                                       text-gray-600 dark:text-gray-300
                                       hover:bg-gray-100 dark:hover:bg-gray-700
                                       border border-gray-200 dark:border-gray-600
                                       transition-colors">
                            @if(app()->getLocale() === 'ms')
                                🇲🇾 <span>BM</span>
                            @elseif(app()->getLocale() === 'zh')
                                🇨🇳 <span>中文</span>
                            @else
                                🇬🇧 <span>EN</span>
                            @endif
                            <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div id="lang-menu"
                             role="menu"
                             aria-orientation="vertical"
                             class="hidden absolute right-0 top-full mt-1.5
                                    bg-white dark:bg-gray-800
                                    rounded-xl shadow-xl
                                    border border-gray-100 dark:border-gray-700
                                    overflow-hidden w-44 z-50">
                            @foreach([
                                ['en', '🇬🇧', 'English'],
                                ['ms', '🇲🇾', 'Bahasa Melayu'],
                                ['zh', '🇨🇳', '中文'],
                            ] as [$code, $flag, $name])
                            <a href="{{ route('lang', $code) }}"
                               role="menuitem"
                               class="flex items-center gap-2 px-4 py-2.5 text-sm transition-colors
                                      {{ app()->getLocale() === $code
                                         ? 'text-brand-red font-semibold bg-red-50 dark:bg-red-900/20'
                                         : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                {{ $flag }} {{ $name }}
                                @if(app()->getLocale() === $code)
                                <svg class="w-3.5 h-3.5 ml-auto text-brand-red" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── Dark / Light mode toggle ── --}}
                    <button id="dark-toggle"
                            aria-label="{{ __('Toggle dark mode') }}"
                            class="p-2 rounded-lg transition-colors
                                   text-gray-600 dark:text-gray-300
                                   hover:bg-gray-100 dark:hover:bg-gray-700
                                   border border-gray-200 dark:border-gray-600">
                        {{-- Sun: shown when dark mode is active --}}
                        <svg id="icon-sun" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="5"/>
                            <line x1="12" y1="1" x2="12" y2="3"/>
                            <line x1="12" y1="21" x2="12" y2="23"/>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                            <line x1="1" y1="12" x2="3" y2="12"/>
                            <line x1="21" y1="12" x2="23" y2="12"/>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                        </svg>
                        {{-- Moon: shown when light mode is active --}}
                        <svg id="icon-moon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                        </svg>
                    </button>

                    {{-- ── Mobile hamburger ── --}}
                    <button id="mobile-menu-btn"
                            class="md:hidden p-2 rounded-lg transition-colors
                                   text-gray-700 dark:text-gray-300
                                   hover:bg-gray-100 dark:hover:bg-gray-700"
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

                </div>{{-- end right controls --}}
            </div>{{-- end flex row --}}

            {{-- ── Mobile menu ── --}}
            <div id="mobile-menu"
                 class="hidden md:hidden pb-4 space-y-1 border-t border-gray-100 dark:border-gray-700 pt-3"
                 role="menu">
                @foreach([
                    [route('home'),     __('Home'),     request()->routeIs('home')],
                    [route('products'), __('Products'), request()->routeIs('products*')],
                    [route('about'),    __('About Us'), request()->routeIs('about')],
                    [route('contact'),  __('Contact'),  request()->routeIs('contact')],
                ] as [$href, $label, $active])
                <a href="{{ $href }}"
                   role="menuitem"
                   class="block py-2.5 px-3 rounded-lg font-medium transition-colors
                          {{ $active
                             ? 'text-brand-red bg-red-50 dark:bg-red-900/20'
                             : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-brand-red' }}"
                   @if($active) aria-current="page" @endif>
                    {{ $label }}
                </a>
                @endforeach
                <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('products') }}"
                       class="block py-2.5 px-3 bg-brand-red text-white rounded-lg font-semibold text-center hover:bg-red-700 transition-colors">
                        {{ __('Shop Now') }}
                    </a>
                </div>
            </div>

        </div>{{-- end max-w container --}}
    </nav>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-300 px-4 py-3 max-w-7xl mx-auto mt-4 rounded-r-lg"
         role="alert" aria-live="polite">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-300 px-4 py-3 max-w-7xl mx-auto mt-4 rounded-r-lg"
         role="alert" aria-live="polite">
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Main content ── --}}
    <main id="main-content" tabindex="-1">
        {{ $slot }}
    </main>

    {{-- ── Footer ── --}}
    <footer class="bg-brand-black text-gray-300 mt-16" role="contentinfo">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">

                {{-- Brand column --}}
                <div class="sm:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 bg-brand-red rounded-full flex items-center justify-center flex-shrink-0" aria-hidden="true">
                            <span class="text-brand-yellow font-black text-lg">W</span>
                        </div>
                        <div>
                            <div class="font-black text-white text-lg">Win Win Car Studio</div>
                            <div class="text-xs text-brand-yellow uppercase tracking-widest">Accessories</div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        {{ __('Your one-stop shop for premium car accessories. We provide high-quality products to make your vehicle look and perform at its best.') }}
                    </p>
                </div>

                {{-- Quick links --}}
                <div>
                    <h4 class="font-bold text-white mb-4 uppercase text-sm tracking-wider">{{ __('Quick Links') }}</h4>
                    <ul class="space-y-2 text-sm" role="list">
                        <li><a href="{{ route('home') }}"     class="hover:text-brand-yellow transition-colors">{{ __('Home') }}</a></li>
                        <li><a href="{{ route('products') }}" class="hover:text-brand-yellow transition-colors">{{ __('Products') }}</a></li>
                        <li><a href="{{ route('about') }}"    class="hover:text-brand-yellow transition-colors">{{ __('About Us') }}</a></li>
                        <li><a href="{{ route('contact') }}"  class="hover:text-brand-yellow transition-colors">{{ __('Contact') }}</a></li>
                    </ul>
                </div>

                {{-- Contact info --}}
                <div>
                    <h4 class="font-bold text-white mb-4 uppercase text-sm tracking-wider">{{ __('Contact Us') }}</h4>
                    <address class="not-italic space-y-2 text-sm">
                        <div class="flex items-start gap-2">
                            <span class="text-brand-yellow mt-0.5 flex-shrink-0" aria-hidden="true">📍</span>
                            <span>123 Jalan Auto, Kuala Lumpur, Malaysia</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-brand-yellow flex-shrink-0" aria-hidden="true">📞</span>
                            <a href="tel:+60123456789" class="hover:text-brand-yellow transition-colors">+60 12-345 6789</a>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-brand-yellow flex-shrink-0" aria-hidden="true">✉️</span>
                            <a href="mailto:info@winwincarstudio.com" class="hover:text-brand-yellow transition-colors break-all">info@winwincarstudio.com</a>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-brand-yellow flex-shrink-0" aria-hidden="true">🕐</span>
                            <span>{{ __('Mon–Sat: 9am – 7pm') }}</span>
                        </div>
                    </address>
                </div>

            </div>

            <div class="border-t border-gray-700 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500 gap-2">
                <p>&copy; {{ date('Y') }} Win Win Car Studio Accessories. {{ __('All rights reserved.') }}</p>
                <p>{{ __('Designed with ❤️ for car enthusiasts') }}</p>
            </div>
        </div>
    </footer>

    <script>
        /* ── Dark / Light mode toggle ── */
        const darkToggle  = document.getElementById('dark-toggle');
        const iconSun     = document.getElementById('icon-sun');
        const iconMoon    = document.getElementById('icon-moon');

        function applyDarkIcon(isDark) {
            iconSun.classList.toggle('hidden', !isDark);
            iconMoon.classList.toggle('hidden', isDark);
            darkToggle.setAttribute(
                'aria-label',
                isDark ? 'Switch to light mode' : 'Switch to dark mode'
            );
        }
        applyDarkIcon(document.documentElement.classList.contains('dark'));

        darkToggle.addEventListener('click', function () {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            applyDarkIcon(isDark);
        });

        /* ── Language dropdown ── */
        const langBtn     = document.getElementById('lang-btn');
        const langMenu    = document.getElementById('lang-menu');
        const langWrapper = document.getElementById('lang-wrapper');

        function openLang()  { langMenu.classList.remove('hidden'); langBtn.setAttribute('aria-expanded', 'true'); }
        function closeLang() { langMenu.classList.add('hidden');    langBtn.setAttribute('aria-expanded', 'false'); }

        langBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            langMenu.classList.contains('hidden') ? openLang() : closeLang();
        });
        document.addEventListener('click', function (e) {
            if (!langWrapper.contains(e.target)) closeLang();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeLang();
        });

        /* ── Mobile menu ── */
        const mobileBtn       = document.getElementById('mobile-menu-btn');
        const mobileMenu      = document.getElementById('mobile-menu');
        const iconHamburger   = document.getElementById('icon-hamburger');
        const iconClose       = document.getElementById('icon-close');

        mobileBtn.addEventListener('click', function () {
            const opening = mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            iconHamburger.classList.toggle('hidden', opening);
            iconClose.classList.toggle('hidden', !opening);
            this.setAttribute('aria-expanded', opening ? 'true' : 'false');
        });
    </script>

    @livewireScripts
</body>
</html>
