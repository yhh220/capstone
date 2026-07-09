<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth {{ request()->cookie('app_theme') === 'dark' ? 'dark' : '' }}">
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
    $mapUrl = 'https://www.google.com/maps?cid=' . config('services.store.place_cid');
    $telLink = 'tel:' . preg_replace('/[^0-9+]/', '', $storePhoneDisplay);
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicons')
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! TwitterCard::generate() !!}
    {!! JsonLd::generate() !!}

    <script>
        (function () {
            var t = localStorage.getItem('site-theme');
            var dark = t === 'dark' || ((!t || t === 'system') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            // localStorage is the source of truth — correct the server-rendered
            // class if needed, and mirror the resolved value into a cookie so the
            // server renders the right class on the next request/soft-navigation
            // (no theme flash when switching language via Livewire.navigate).
            document.documentElement.classList.toggle('dark', dark);
            document.documentElement.style.backgroundColor = dark ? '#121212' : '#F7F5F3';
            document.cookie = 'app_theme=' + (dark ? 'dark' : 'light') + '; path=/; max-age=31536000; SameSite=Lax';
        })();
    </script>
    <script>
        // iOS Safari only renders :active/:hover on tap if a touch listener
        // exists somewhere in the document — otherwise every active: utility
        // (hamburger, buttons, etc.) silently never shows on a real iPhone.
        document.addEventListener('touchstart', function () {}, false);
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    {{-- Leaflet CSS/JS are loaded only on the Contact page (the only page with a
         map), via @push there — not globally — so every other page skips ~46 KB
         of render-blocking CSS + script it never uses. --}}
    @vite('resources/css/app.css')

    <style>
        h1, h2, h3, h4, h5, h6, .font-display {
            font-family: 'Anton', sans-serif !important;
            font-weight: 400 !important;
            text-transform: uppercase;
            letter-spacing: 0;
        }

        html:not([lang^="en"]) h1,
        html:not([lang^="en"]) h2,
        html:not([lang^="en"]) h3,
        html:not([lang^="en"]) h4,
        html:not([lang^="en"]) h5,
        html:not([lang^="en"]) h6,
        html:not([lang^="en"]) .font-display,
        html:not([lang^="en"]) .uppercase {
            text-transform: none !important;
        }

        html:not([lang^="en"]) [class*="tracking-"] {
            letter-spacing: 0 !important;
        }

        #main-content a,
        #main-content button,
        #main-content [role="button"] {
            min-width: 0;
        }

        #main-content a[class*="inline-flex"],
        #main-content button[class*="inline-flex"],
        #main-content a[class*="flex"],
        #main-content button[class*="flex"] {
            white-space: normal;
            overflow-wrap: anywhere;
        }

        #main-content svg {
            flex-shrink: 0;
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
            /* Dedicated to .btn-primary's fill — kept separate from --brand-red-rgb
               because that token also colors ghost/secondary button TEXT on a dark
               background, where the lighter Ember Light is what keeps ITS contrast
               passing. Reusing it for the primary button's white-on-fill background
               would fix one and break the other, so the button gets its own pair,
               fixed to the values that keep white text >= 4.5:1 in both themes. */
            --btn-primary-bg-rgb: 200 65 61;       /* #C8413D — Ember Red, white text = 4.91:1 */
            --btn-primary-bg-hover-rgb: 168 52 50; /* #A83432 — Ember Dark, white text = 6.56:1 */
        }
        .dark {
            --brand-red-rgb: 232 100 96;      /* #E86460 — Ember Light on dark */
            --brand-yellow-rgb: 232 224 216;  /* #E8E0D8 — Bone White accent */
            --brand-black-rgb: 28 25 23;      /* #1C1917 — Asphalt surface */
            --brand-red-hover-rgb: 200 65 61; /* #C8413D — Ember Red hover on dark */
            /* Unchanged from :root — the lighter Ember Light used elsewhere in dark
               mode (--brand-red-rgb) only gave white button text 3.27:1, below WCAG
               AA's 4.5:1. Kept identical to light mode so .btn-primary passes in both. */
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

        /* ── Shimmer skeleton ────────────────────────────── */
        @keyframes shimmer {
            from { background-position: -200% 0; }
            to   { background-position:  200% 0; }
        }
        .skeleton {
            background-image: linear-gradient(100deg,
                rgba(0,0,0,0.06) 30%, rgba(0,0,0,0.12) 50%, rgba(0,0,0,0.06) 70%);
            background-size: 200% 100%;
            animation: shimmer 1.4s ease-in-out infinite;
            border-radius: 0.375rem;
        }
        .dark .skeleton {
            background-image: linear-gradient(100deg,
                rgba(255,255,255,0.05) 30%, rgba(255,255,255,0.11) 50%, rgba(255,255,255,0.05) 70%);
        }
        @media (prefers-reduced-motion: reduce) {
            .skeleton { animation: none; }
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

        /* ── Theme segmented control — filled brand-red active state ───────── */
        .theme-seg-active {
            background: rgb(var(--brand-red-rgb));
            box-shadow: 0 2px 10px rgba(var(--brand-red-rgb), 0.45);
            color: #fff !important;
        }

        /* Language trigger chevron flips while the menu is open */
        .lang-chevron { transition: transform 0.25s ease; }
        #lang-btn[aria-expanded="true"] .lang-chevron { transform: rotate(180deg); }

        /* Cart badge pops when the count changes */
        @keyframes cartBump {
            0% { transform: scale(0.6); }
            55% { transform: scale(1.25); }
            100% { transform: scale(1); }
        }
        .cart-badge-bump { animation: cartBump 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); }
        @media (prefers-reduced-motion: reduce) { .cart-badge-bump { animation: none; } }

        /* Hide scrollbars on horizontal scroll rows (chips, carousels) */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        /* Chat window sits above the sticky nav and never taller than the space
           between the nav and its bottom offset, so its header is never hidden
           behind the header on short (laptop) viewports. */
        .chatbot-window { z-index: 9999 !important; }
        @media (min-width: 768px) {
            .chatbot-window { max-height: calc(100vh - 12rem) !important; }
        }

        /* ═══════════════════════════════════════════════
           UNIFIED BUTTON SYSTEM
           Primary   .btn-primary   — red fill, white text
           Secondary .btn-secondary — bordered, red on hover
           Ghost     .btn-ghost     — transparent, red on hover
           Sizes     .btn-sm / .btn-md / .btn-lg
        ═══════════════════════════════════════════════ */

        /* Base */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 700;
            border-radius: 0.75rem;   /* rounded-xl — matches contact page */
            border: 2px solid transparent;
            cursor: pointer;
            text-decoration: none;
            white-space: normal;
            overflow-wrap: anywhere;
            transition: transform 0.3s ease, box-shadow 0.3s ease,
                        background-color 0.3s ease, border-color 0.3s ease,
                        color 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn:active { transform: scale(0.95) !important; }
        .btn:disabled, .btn[disabled] { opacity: 0.5; pointer-events: none; }

        /* Sizes */
        .btn-sm  { padding: 0.5rem 1rem;      font-size: 0.8125rem; }
        .btn-md  { padding: 0.625rem 1.5rem;  font-size: 0.875rem;  }
        .btn-lg  { padding: 0.75rem 2rem;     font-size: 1rem;      font-weight: 900; }
        .btn-xl  { padding: 0.875rem 2.25rem; font-size: 1.0625rem; font-weight: 900; }

        /* Pill variant */
        .btn-pill { border-radius: 9999px; }

        /* Primary — red fill. Uses --btn-primary-bg-rgb (not --brand-red-rgb) so the
           white button text stays >= 4.5:1 in dark mode too — see the :root comment. */
        .btn-primary {
            background-color: rgb(var(--btn-primary-bg-rgb));
            color: #fff;
            box-shadow: 0 6px 20px rgba(var(--btn-primary-bg-rgb), 0.35);
        }
        .btn-primary:hover {
            background-color: rgb(var(--btn-primary-bg-hover-rgb));
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(var(--btn-primary-bg-rgb), 0.5);
        }

        /* Secondary — bordered, red on hover */
        .btn-secondary {
            background-color: transparent;
            border-color: rgb(220 220 220);
            color: rgb(55 65 81);
        }
        .dark .btn-secondary {
            border-color: rgb(75 85 99);
            color: rgb(209 213 219);
        }
        .btn-secondary:hover {
            border-color: rgb(var(--brand-red-rgb));
            color: rgb(var(--brand-red-rgb));
            background-color: rgba(249, 250, 251, 0.8);
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .dark .btn-secondary:hover {
            background-color: rgba(31, 41, 55, 0.8);
        }

        /* Ghost — text only */
        .btn-ghost {
            background-color: transparent;
            color: rgb(var(--brand-red-rgb));
        }
        .btn-ghost:hover {
            background-color: rgba(var(--brand-red-rgb), 0.08);
            transform: translateY(-4px);
        }

        /* Icon button */
        .btn-icon { padding: 0.5rem; border-radius: 0.5rem; gap: 0; }

        /* Brand variants — WhatsApp green / Facebook blue, same lift + shine */
        .btn-whatsapp {
            background-color: #25D366;
            color: #fff;
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.30);
        }
        .btn-whatsapp:hover {
            background-color: #1ebe57;
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(37, 211, 102, 0.45);
        }
        .btn-facebook {
            background-color: #1877F2;
            color: #fff;
            box-shadow: 0 6px 20px rgba(24, 119, 242, 0.30);
        }
        .btn-facebook:hover {
            background-color: #1467d8;
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(24, 119, 242, 0.45);
        }
        /* Stripe blurple — the pay button wears Stripe's own brand colour so
           customers recognise where the redirect is taking them. */
        .btn-stripe {
            background-color: #635BFF;
            color: #fff;
            box-shadow: 0 6px 20px rgba(99, 91, 255, 0.30);
        }
        .btn-stripe:hover {
            background-color: #5851ea;
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(99, 91, 255, 0.45);
        }

        /* Subtle outline button for dark surfaces (e.g. the dark CTA card) —
           muted by default so it sits behind the primary action, brightening
           only on hover. */
        .btn-outline-light {
            background-color: transparent;
            border-color: rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.72);
        }
        .btn-outline-light:hover {
            border-color: rgba(255, 255, 255, 0.30);
            background-color: rgba(255, 255, 255, 0.06);
            color: #fff;
            transform: translateY(-4px);
        }

        /* Icon inside a .btn gently pops on hover (unified across all buttons) */
        .btn-ico { transition: transform 0.3s ease; position: relative; z-index: 1; }
        .btn:hover .btn-ico { transform: scale(1.12); }

        /* Shine overlay — skewed sweep matching contact page span pattern */
        .btn-shine::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0.25);
            transform: skewX(45deg) translateX(-150%);
            transition: transform 0.7s ease;
        }
        .btn-shine:hover::after { transform: skewX(45deg) translateX(150%); }

        /* Arrow nudge */
        .btn:hover .icon-arrow { transform: translateX(3px); }

        /* ═══════════════════════════════════════════════
           UNIFIED ICON SYSTEM  (Lucide)
           .icon-xs  12px   .icon-sm  16px
           .icon-md  20px   .icon-lg  24px
           Icons inherit currentColor — set color on parent.
           Use .icon-spin for loading, .icon-bounce on hover.
        ═══════════════════════════════════════════════ */

        .icon-xs { width: 0.75rem;  height: 0.75rem;  flex-shrink: 0; }
        .icon-sm { width: 1rem;     height: 1rem;     flex-shrink: 0; }
        .icon-md { width: 1.25rem;  height: 1.25rem;  flex-shrink: 0; }
        .icon-lg { width: 1.5rem;   height: 1.5rem;   flex-shrink: 0; }
        .icon-xl { width: 2rem;     height: 2rem;     flex-shrink: 0; }

        .icon-spin { animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Arrow nudge — put on icon inside a .btn */
        .btn:hover .icon-arrow { transform: translateX(3px); }
        .icon-arrow { transition: transform 0.2s ease; }

        /* ═══════════════════════════════════════════════
           CARD HOVER (itshover.com pattern)
           .card-lift  — translate up + shadow
           .card-glow  — brand-red glow ring
        ═══════════════════════════════════════════════ */

        .card-lift {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-lift:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.10);
        }
        .dark .card-lift:hover {
            box-shadow: 0 16px 40px rgba(0,0,0,0.35);
        }
        .card-glow {
            transition: box-shadow 0.25s ease;
        }
        .card-glow:hover {
            box-shadow: 0 0 0 3px rgb(var(--brand-red-rgb) / 0.25),
                        0 8px 24px rgba(0,0,0,0.10);
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
        // Initial badge count; JS keeps #cart-count-badge live on 'cart-updated'.
        $cartCount = $shoppingEnabled ? (int) \App\Models\CartItem::forCurrentOwner()->sum('quantity') : 0;
    @endphp

    <nav x-data="{ cartOpen: false }"
         @open-cart.window="cartOpen = true"
         @require-signin.window="cartOpen = false"
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
                        {{-- fetchpriority=high: the logo is the LCP element on most
                             pages; without it the browser deprioritises the SVG and
                             LCP is delayed. Not lazy-loaded (it is above the fold). --}}
                        <img src="{{ asset('images/logo/logo-dark.svg') }}" alt="" fetchpriority="high" class="h-full w-full object-contain block dark:hidden">
                        <img src="{{ asset('images/logo/logo-light.svg') }}" alt="" fetchpriority="high" class="h-full w-full object-contain hidden dark:block">
                    </div>
                    <div class="mt-0.5 sm:mt-1 hidden sm:flex items-center gap-1.5 leading-none transition-opacity duration-300 group-hover:opacity-80">
                        <span class="font-black text-brand-black dark:text-white text-[11px] uppercase tracking-[0.15em]">{{ $storeShortName }}</span>
                        <span class="text-[10px] text-brand-red font-semibold uppercase tracking-widest">{{ $storeTagline }}</span>
                    </div>
                </a>

                <!-- Desktop Nav (7 links) — shows at lg+ so the longer BM labels
                     (Laman Utama, Perkhidmatan…) don't overflow at tablet widths -->
                {{-- No role="list" here: it requires role="listitem" children, but
                     adding that to the <a> links would override their "link" role.
                     These are nav links inside <nav>, so the list semantics are
                     unnecessary — a broken list tree is worse than none. --}}
                <div class="hidden lg:flex items-center gap-0.5">
                    @foreach([
                        [route('home'),     __('Home'),     request()->routeIs('home')],
                        [route('products'), __('Products'), request()->routeIs('products*')],
                        [route('services'), __('Services'), request()->routeIs('services')],
                        [route('booking'),  __('Booking'),  request()->routeIs('booking*')],
                        [route('about'),    __('About'),    request()->routeIs('about')],
                        [route('contact'),  __('Contact'),  request()->routeIs('contact')],
                    ] as [$href, $label, $active])
                    <a href="{{ $href }}"
                       class="relative px-2.5 py-1.5 lg:px-3 lg:py-2 xl:px-4 rounded-full text-xs lg:text-sm font-bold transition-all duration-300 ease-out whitespace-nowrap overflow-hidden
                              {{ $active ? 'text-brand-red bg-red-50 dark:bg-red-900/10' : 'text-gray-600 dark:text-gray-300 hover:text-brand-red after:content-[\'\'] after:absolute after:bottom-1 after:left-1/2 after:-translate-x-1/2 after:w-0 hover:after:w-3/4 after:h-[2px] after:bg-brand-red after:transition-all after:duration-300' }}"
                       @if($active) aria-current="page" @endif>
                        {{ $label }}
                    </a>
                    @endforeach
                </div>

                <!-- Right-side controls -->
                <div class="flex items-center gap-1 sm:gap-1.5">
                    <div class="flex items-center gap-1 sm:gap-1.5">
                    <!-- Language switcher -->
                    <div class="relative" id="lang-wrapper">
                        <button id="lang-btn"
                                aria-label="{{ __('Select language') }}"
                                aria-expanded="false"
                                aria-haspopup="true"
                                class="flex items-center gap-1.5 h-9 px-2.5 sm:px-3 rounded-lg text-xs font-extrabold uppercase tracking-wider text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-800 hover:text-brand-red active:scale-95 transition-all duration-200">
                            <svg class="w-3.5 h-3.5 text-brand-red" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                            </svg>
                            <span>
                                @if(app()->getLocale() === 'ms') BM
                                @elseif(app()->getLocale() === 'zh') 中文
                                @else EN
                                @endif
                            </span>
                            <svg class="lang-chevron w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>

                        <div id="lang-menu"
                             role="menu"
                             aria-orientation="vertical"
                             class="hidden absolute right-0 top-full mt-2 w-48 rounded-xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden z-50 bg-white dark:bg-gray-900 lang-menu-enter">
                            <div class="p-1.5 space-y-0.5">
                                @foreach([
                                    ['en', 'English',       'EN'],
                                    ['ms', 'Bahasa Melayu', 'BM'],
                                    ['zh', '中文',           'ZH'],
                                ] as [$code, $name, $short])
                                @php $isActive = app()->getLocale() === $code; @endphp
                                <button type="button"
                                   role="menuitem"
                                   data-lang-code="{{ $code }}"
                                   data-lang-url="{{ route('lang', $code) }}"
                                   class="lang-option w-full text-left flex items-center gap-2.5 px-2 py-1.5 rounded-lg active:scale-95 transition-all duration-150 {{ $isActive ? 'bg-brand-red-solid text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 active:bg-gray-200 dark:active:bg-gray-700' }}">
                                    <span class="w-8 shrink-0 text-center text-[10px] font-black uppercase tracking-wider rounded py-1 {{ $isActive ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300' }}">{{ $short }}</span>
                                    <span class="flex-1 text-sm font-bold">{{ $name }}</span>
                                    @if($isActive)
                                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Theme switcher (segmented control) — hidden on xs, visible lg+ -->
                    <div id="theme-wrapper" class="hidden lg:flex items-center h-9 p-0.5 rounded-lg bg-gray-100 dark:bg-gray-900 gap-0.5">
                        <button class="theme-option w-8 h-full flex items-center justify-center rounded-md active:scale-90 transition-all duration-200 text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-200" data-theme="light" title="{{ __('Light mode') }}" aria-label="{{ __('Light mode') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
                            </svg>
                        </button>
                        <button class="theme-option w-8 h-full flex items-center justify-center rounded-md active:scale-90 transition-all duration-200 text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-200" data-theme="dark" title="{{ __('Dark mode') }}" aria-label="{{ __('Dark mode') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                            </svg>
                        </button>
                        <button class="theme-option w-8 h-full flex items-center justify-center rounded-md active:scale-90 transition-all duration-200 text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-200" data-theme="system" title="{{ __('System theme') }}" aria-label="{{ __('System theme') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/>
                            </svg>
                        </button>
                    </div>
                    </div>

                    @if($shoppingEnabled || auth()->check())
                    <div class="hidden lg:block h-7 w-px bg-gray-200 dark:bg-gray-700" aria-hidden="true"></div>
                    @endif

                    <div class="flex items-center gap-1 sm:gap-1.5">
                    <!-- User dropdown (desktop) -->
                    @if($shoppingEnabled || auth()->check())
                    <div class="hidden lg:block relative" x-data="{ open: false }">
                        <x-tooltip text="{{ auth()->check() ? Auth::user()->name : __('Account') }}" position="bottom">
                        <button @click="open = !open"
                                @click.outside="open = false"
                                type="button"
                                class="group flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition-all duration-300 hover:shadow-sm"
                                aria-label="{{ __('User menu') }}">
                            @auth
                                <div class="w-6 h-6 rounded-full bg-brand-red-solid flex items-center justify-center text-white text-[11px] font-black group-hover:scale-110 transition-transform duration-300">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @else
                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300 transition-transform duration-300 group-hover:scale-110 group-hover:-translate-y-0.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            @endauth
                        </button>
                        </x-tooltip>

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
                                    <a href="{{ route('account') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        {{ __('My Account') }}
                                    </a>
                                    @if($shoppingEnabled)
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
                                </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                    @endif

                    <!-- Cart icon + live count (only when shopping enabled) -->
                    @if($shoppingEnabled)
                    <x-tooltip text="{{ __('Cart') }}" position="bottom">
                    <button type="button"
                            @click="cartOpen = true"
                            aria-label="{{ __('Open cart') }}"
                            class="group relative p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-brand-red-solid hover:text-white border border-transparent hover:border-brand-red active:scale-90 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                        <svg class="w-4 h-4 transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-[10deg]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                        </svg>
                        <span id="cart-count-badge"
                              @if($cartCount <= 0) style="display:none;" @endif
                              class="absolute -top-1 -right-1 bg-brand-red-solid text-white text-[10px] leading-none font-bold rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center shadow-sm group-hover:bg-white group-hover:text-brand-red">
                            {{ $cartCount > 9 ? '9+' : $cartCount }}
                        </span>
                    </button>
                    </x-tooltip>
                    @endif
                    </div>

                    <!-- Mobile menu button (Lucide Animated) -->
                    <button id="mobile-menu-btn"
                            class="lg:hidden group p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-transparent hover:border-gray-200 dark:hover:border-gray-600 active:scale-90 transition-all duration-300"
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
                 class="hidden lg:hidden pb-4 space-y-1 border-t border-gray-100 dark:border-gray-700 pt-3"
                 role="menu">

                <!-- Theme toggle inside mobile menu (xs/sm/md screens) -->
                <div class="lg:hidden flex items-center gap-2 px-3 py-2 mb-1">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 mr-1">{{ __('Theme') }}:</span>
                    <div class="flex items-center h-9 p-0.5 rounded-lg bg-gray-100 dark:bg-gray-900 gap-0.5">
                        <button class="theme-option w-8 h-full flex items-center justify-center rounded-md active:scale-90 transition-all duration-200 text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-200" data-theme="light" title="{{ __('Light mode') }}" aria-label="{{ __('Light mode') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                        </button>
                        <button class="theme-option w-8 h-full flex items-center justify-center rounded-md active:scale-90 transition-all duration-200 text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-200" data-theme="dark" title="{{ __('Dark mode') }}" aria-label="{{ __('Dark mode') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                        </button>
                        <button class="theme-option w-8 h-full flex items-center justify-center rounded-md active:scale-90 transition-all duration-200 text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-200" data-theme="system" title="{{ __('System theme') }}" aria-label="{{ __('System theme') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/></svg>
                        </button>
                    </div>
                </div>
                @foreach([
                    [route('home'),     __('Home'),     request()->routeIs('home')],
                    [route('products'), __('Products'), request()->routeIs('products*')],
                    [route('services'), __('Services'), request()->routeIs('services')],
                    [route('booking'),  __('Booking'),  request()->routeIs('booking*')],
                    [route('about'),    __('About'),    request()->routeIs('about')],
                    [route('contact'),  __('Contact'),  request()->routeIs('contact')],
                ] as [$href, $label, $active])
                <a href="{{ $href }}"
                   role="menuitem"
                   class="block py-2.5 px-3 rounded-lg font-medium transition-colors {{ $active ? 'text-brand-red bg-red-50 dark:bg-red-900/20 active:bg-red-100 dark:active:bg-red-900/40' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-brand-red active:bg-gray-100 dark:active:bg-gray-600' }}"
                   @if($active) aria-current="page" @endif>
                    {{ $label }}
                </a>
                @endforeach

                @if($shoppingEnabled || auth()->check())
                <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                    @auth
                        <div class="px-3 py-2 mb-1">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-brand-red-solid flex items-center justify-center text-white text-sm font-black">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-gray-800 dark:text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-400">{{ ucfirst(Auth::user()->role) }}</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 active:bg-gray-100 dark:active:bg-gray-600 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ __('My Profile') }}
                        </a>
                        <a href="{{ route('account') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 active:bg-gray-100 dark:active:bg-gray-600 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            {{ __('My Account') }}
                        </a>
                        @if($shoppingEnabled)
                        <a href="{{ route('track-order') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 active:bg-gray-100 dark:active:bg-gray-600 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            {{ __('Track Order') }}
                        </a>
                        @endif
                        <a href="{{ route('booking.track') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 active:bg-gray-100 dark:active:bg-gray-600 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ __('Track Booking') }}
                        </a>
                        @if(Auth::user()->isAdmin())
                        <a href="/admin" class="flex items-center gap-3 py-2.5 px-3 rounded-lg font-semibold text-brand-red hover:bg-red-50 dark:hover:bg-red-900/20 active:bg-red-100 dark:active:bg-red-900/40 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ __('Admin Dashboard') }}
                        </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full py-2.5 px-3 rounded-lg font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 active:bg-red-100 dark:active:bg-red-900/40 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                {{ __('Sign Out') }}
                            </button>
                        </form>
                    @else
                        @if($shoppingEnabled)
                        <a href="{{ route('login') }}" class="block py-2.5 px-3 rounded-lg font-medium text-brand-red hover:bg-red-50 dark:hover:bg-red-900/20 active:bg-red-100 dark:active:bg-red-900/40 transition-colors">{{ __('Sign In') }}</a>
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
                            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 active:scale-90 transition-all duration-200 text-gray-600 dark:text-gray-300"
                            aria-label="{{ __('Close cart') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="flex-1">
                    @if(!request()->is('cart'))
                        <livewire:cart-page :compact="true" />
                    @endif
                </div>
            </aside>
        </div>

        {{-- Sign-in-required prompt — fires on the 'require-signin' window event --}}
        <div x-data="{ show: false }"
             @require-signin.window="show = true"
             @keydown.escape.window="show = false"
             x-show="show" x-cloak
             class="fixed inset-0 z-[90] flex items-center justify-center px-4"
             style="display:none;" role="dialog" aria-modal="true">
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="show = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 p-7 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-brand-red/10 text-brand-red flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">{{ __('Please sign in first') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ __('You need to sign in to continue to checkout.') }}</p>
                <a href="{{ route('checkout') }}" class="btn btn-primary btn-md btn-shine w-full !rounded-xl">
                    {{ __('Sign In') }}
                </a>
                <button type="button" @click="show = false" class="block w-full text-sm text-gray-500 dark:text-gray-400 hover:text-brand-red transition-colors py-2.5 mt-1 font-semibold">
                    {{ __('Maybe later') }}
                </button>
            </div>
        </div>

        {{-- Add-to-cart toast — fires on the 'cart-added' Livewire event --}}
        <div x-data="{ show: false, timer: null }"
             @cart-toast.window="show = true; clearTimeout(timer); timer = setTimeout(() => show = false, 2400)"
             x-show="show"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-3"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-3"
             class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[70] flex items-center gap-3 bg-gray-900 dark:bg-gray-800 text-white pl-4 pr-2 py-2 rounded-full shadow-2xl ring-1 ring-white/10"
             role="status" aria-live="polite" style="display:none;">
            <svg class="w-5 h-5 text-green-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
            <span class="text-sm font-semibold">{{ __('Added to cart') }}</span>
            <button type="button" @click="$dispatch('open-cart'); show = false"
                    class="text-xs font-bold bg-brand-red-solid hover:bg-red-700 text-white px-3 py-1.5 rounded-full transition-colors">
                {{ __('View cart') }}
            </button>
        </div>
        @endif
    </nav>

    {{-- ── Site-wide announcement bar (admin-controlled) ──────────────────
         Toggled by the SITE_ANNOUNCEMENT_ENABLED setting; text from
         SITE_ANNOUNCEMENT_TEXT. Dismissible per-visitor, keyed by the message
         text itself so editing the announcement re-shows it to everyone. --}}
    @php
        $announcementOn   = setting('SITE_ANNOUNCEMENT_ENABLED', 'false') === 'true';
        $announcementText = trim((string) setting('SITE_ANNOUNCEMENT_TEXT', ''));
    @endphp
    @if($announcementOn && $announcementText !== '')
    <div x-data="{
             text: @js($announcementText),
             show: false,
             init() { this.show = localStorage.getItem('site-announce-dismissed') !== this.text; },
             dismiss() { localStorage.setItem('site-announce-dismissed', this.text); this.show = false; }
         }"
         x-show="show"
         x-cloak
         role="status"
         aria-live="polite"
         class="bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800/40">
        <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-start sm:items-center gap-3">
            <svg class="w-4 h-4 shrink-0 mt-0.5 sm:mt-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                <path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
            </svg>
            <p class="flex-1 min-w-0 text-sm text-amber-800 dark:text-amber-300 leading-snug break-words">{{ $announcementText }}</p>
            <button @click="dismiss()"
                    type="button"
                    aria-label="{{ __('Dismiss announcement') }}"
                    class="shrink-0 -mr-1 p-1 rounded-md text-amber-500 hover:text-amber-700 dark:hover:text-amber-200 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-colors active:scale-90">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

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

    {{-- Social-login accounts have no password yet — prompt them to set one before
         they can shop. Disappears automatically once a password is set. --}}
    @auth
        @if(! auth()->user()->hasPassword())
        <a href="{{ route('profile') }}"
           class="block bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800/40 hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">
            <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-center justify-center gap-2 text-sm text-amber-800 dark:text-amber-300 text-center">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <span>{{ __('Set a password to enable shopping & checkout.') }}</span>
                <span class="font-bold underline whitespace-nowrap">{{ __('Set it now') }} →</span>
            </div>
        </a>
        @endif
    @endauth

    <main id="main-content" tabindex="-1">
        {{ $slot }}
    </main>

    {{-- Auth pages are full-screen (min-h-screen); the marketing footer would sit
         after a big empty gap, so it's hidden on those focused screens. --}}
    @unless(request()->routeIs('login', 'password.request'))
    <footer class="bg-brand-black text-gray-300 mt-16" role="contentinfo">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
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
                            <x-icon.whatsapp class="w-4 h-4 text-[#25D366] flex-shrink-0 group-hover:-translate-y-1 group-hover:scale-110 group-hover:rotate-[15deg] transition-transform duration-300" />
                            <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-brand-yellow transition-colors">{{ __('WhatsApp us') }}</a>
                        </div>
                        @if($storeFacebookUrl)
                        <div class="flex items-center gap-2 group">
                            <!-- Premium Facebook SVGL outline icon -->
                            <x-icon.facebook class="w-4 h-4 text-[#1877F2] group-hover:-translate-y-1 group-hover:scale-110 transition-transform duration-300" />
                            <a href="{{ $storeFacebookUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-brand-yellow transition-colors group-hover:tracking-wider duration-300">Facebook</a>
                        </div>
                        @endif
                        @if($storeHours)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-yellow flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                            <span>{{ $storeHours }}</span>
                        </div>
                        @endif
                    </address>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500 gap-3">
                <p>&copy; {{ $storeName }} {{ date('Y') }}. <span>{{ __('All rights reserved.') }}</span></p>
                <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1">
                    <a href="{{ route('privacy-policy') }}" class="hover:text-brand-yellow transition-colors">{{ __('Privacy Policy') }}</a>
                    <span aria-hidden="true" class="hidden sm:inline">·</span>
                    <a href="{{ route('terms-of-service') }}" class="hover:text-brand-yellow transition-colors">{{ __('Terms of Service') }}</a>
                    <span aria-hidden="true" class="hidden sm:inline">·</span>
                    <a href="{{ route('cancellation-refund-policy') }}" class="hover:text-brand-yellow transition-colors">{{ __('Cancellation & Refund Policy') }}</a>
                    <span aria-hidden="true" class="hidden sm:inline">·</span>
                    <a href="{{ route('faq') }}" class="hover:text-brand-yellow transition-colors">{{ __('FAQ') }}</a>
                </div>
            </div>
        </div>
    </footer>
    @endunless

    <script>
    (function () {
        // Bind once. The header lives in the layout and survives Livewire's
        // wire:navigate morphs, so re-running this on every navigation used to
        // redeclare these top-level consts (SyntaxError → all header buttons
        // dead after a language switch). Delegated listeners on `document`
        // keep working no matter how the header DOM is morphed.
        if (window.__headerInit) return;
        window.__headerInit = true;

        function updateThemeSegment(theme) {
            document.querySelectorAll('.theme-option').forEach(function (btn) {
                btn.classList.toggle('theme-seg-active', btn.dataset.theme === theme);
            });
        }
        function applyTheme(theme) {
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            var dark = theme === 'system' ? prefersDark : theme === 'dark';

            // Suppress all transitions for the instant of the switch. Without this,
            // elements with a `transition` (e.g. the sign in / sign up inputs, which
            // start near-white as bg-gray-50) visibly FADE from light to dark — read
            // as a white flash. We kill transitions, flip the theme, force a paint,
            // then restore them so the change is instant and flash-free.
            var killTransitions = document.createElement('style');
            killTransitions.appendChild(document.createTextNode(
                '*,*::before,*::after{transition:none !important;}'
            ));
            document.head.appendChild(killTransitions);

            document.documentElement.classList.toggle('dark', dark);
            // Keep the root background in sync (same as the head script) so the
            // light <html> can't flash through for a frame when toggling to dark.
            document.documentElement.style.backgroundColor = dark ? '#121212' : '#F7F5F3';
            localStorage.setItem('site-theme', theme);
            // Mirror the resolved theme into a cookie so the server renders the
            // matching class and Livewire soft-navigations never flash light.
            document.cookie = 'app_theme=' + (dark ? 'dark' : 'light') + '; path=/; max-age=31536000; SameSite=Lax';
            updateThemeSegment(theme);

            // Force a synchronous reflow so the new colors paint with transitions
            // off, then restore transitions after the next frame.
            void document.documentElement.offsetHeight;
            requestAnimationFrame(function () {
                requestAnimationFrame(function () { killTransitions.remove(); });
            });
        }
        function syncTheme() { applyTheme(localStorage.getItem('site-theme') || 'system'); }

        function openLang() {
            var menu = document.getElementById('lang-menu'), btn = document.getElementById('lang-btn');
            if (!menu) return;
            menu.classList.remove('hidden');
            menu.classList.remove('lang-menu-enter');
            void menu.offsetWidth; // reflow to restart animation
            menu.classList.add('lang-menu-enter');
            if (btn) btn.setAttribute('aria-expanded', 'true');
        }
        function closeLang() {
            var menu = document.getElementById('lang-menu'), btn = document.getElementById('lang-btn');
            if (!menu) return;
            menu.classList.add('hidden');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        }
        function toggleMobile() {
            var menu = document.getElementById('mobile-menu'),
                ham = document.getElementById('icon-hamburger'),
                close = document.getElementById('icon-close'),
                btn = document.getElementById('mobile-menu-btn');
            if (!menu) return;
            var opening = menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            if (ham) ham.classList.toggle('hidden', opening);
            if (close) close.classList.toggle('hidden', !opening);
            if (btn) btn.setAttribute('aria-expanded', opening ? 'true' : 'false');
        }

        // Set the session locale, then soft-navigate (no full reload).
        // Guards: abort an in-flight switch if a newer one starts (so a slow
        // earlier request can't land after a later one — race guard), and
        // fail-fast with a hard reload if the request hangs past 5s.
        var langAbort = null;
        function switchLang(url, code) {
            if (langAbort) langAbort.abort();
            var controller = new AbortController();
            langAbort = controller;
            var timedOut = false;
            var timer = setTimeout(function () { timedOut = true; controller.abort(); }, 5000);

            fetch(url, { redirect: 'follow', credentials: 'same-origin', signal: controller.signal })
                .then(function () {
                    clearTimeout(timer);
                    if (langAbort !== controller) return; // superseded by a newer switch
                    // Keep <html lang> in sync with the just-selected locale so the
                    // persisted page-loader shows the correct language during the
                    // soft-navigation (it reads document.documentElement.lang).
                    if (code) document.documentElement.lang = code;
                    if (window.Livewire && typeof Livewire.navigate === 'function') {
                        // Switching language only swaps text — keep the reader exactly
                        // where they are. Livewire's navigate supports this natively.
                        Livewire.navigate(location.href, { preserveScroll: true });
                    } else {
                        location.reload();
                    }
                })
                .catch(function () {
                    clearTimeout(timer);
                    // Only recover if THIS is still the latest attempt and it timed
                    // out; an abort from a newer click is handled by that click.
                    if (langAbort === controller && timedOut) location.reload();
                });
        }

        document.addEventListener('click', function (e) {
            var themeOpt = e.target.closest('.theme-option');
            if (themeOpt) { applyTheme(themeOpt.dataset.theme); return; }

            if (e.target.closest('#lang-btn')) {
                e.stopPropagation();
                var menu = document.getElementById('lang-menu');
                (menu && menu.classList.contains('hidden')) ? openLang() : closeLang();
                return;
            }

            var langOpt = e.target.closest('.lang-option');
            if (langOpt) {
                e.preventDefault();
                closeLang();
                switchLang(langOpt.dataset.langUrl, langOpt.dataset.langCode);
                return;
            }

            if (e.target.closest('#mobile-menu-btn')) { toggleMobile(); return; }

            // Click outside the language switcher closes its menu.
            if (!e.target.closest('#lang-wrapper')) closeLang();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeLang();
        });

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
            if (localStorage.getItem('site-theme') === 'system') applyTheme('system');
        });

        syncTheme();
        // Re-assert the theme after a soft navigation (morph can reset the class).
        document.addEventListener('livewire:navigated', syncTheme);
    })();
    </script>
    
    @livewire('chatbot')

    @livewireScripts

    {{-- Cart events: instantly update the header badge from the response payload
         (no extra round-trip) and toast on add. --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('cart-updated', (e) => {
                var count = 0;
                if (e && typeof e === 'object') {
                    count = ('count' in e) ? e.count : (Array.isArray(e) && e[0] ? (e[0].count || 0) : 0);
                }
                count = parseInt(count, 10) || 0;
                var badge = document.getElementById('cart-count-badge');
                if (!badge) return;
                if (count > 0) {
                    badge.textContent = count > 9 ? '9+' : count;
                    badge.style.display = '';
                    badge.classList.remove('cart-badge-bump');
                    void badge.offsetWidth;            // restart the pop animation
                    badge.classList.add('cart-badge-bump');
                } else {
                    badge.style.display = 'none';
                }
            });
            Livewire.on('cart-added', () => window.dispatchEvent(new CustomEvent('cart-toast')));

            // A wire:click action can swap a tall block of content (e.g. an order
            // summary) for a short success card without resetting the browser's
            // scroll offset, leaving the user stranded below the new (shorter)
            // content — visually "in the footer". Scroll back up so the result is
            // actually visible.
            Livewire.on('scroll-top', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
        });
    </script>

    {{-- ── Themed confirmation modal (replaces native browser confirm) ───── --}}
    <div x-data x-show="$store.confirm.show" x-cloak style="display:none;"
         class="fixed inset-0 z-[120] flex items-center justify-center p-4"
         @keydown.escape.window="$store.confirm.cancel()"
         role="dialog" aria-modal="true" aria-labelledby="confirm-modal-message">
        <div x-show="$store.confirm.show" x-transition.opacity.duration.200ms
             class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$store.confirm.cancel()"></div>
        <div x-show="$store.confirm.show"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-start gap-3 mb-6">
                <span class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-red/10 text-brand-red shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
                </span>
                <p id="confirm-modal-message" class="text-sm leading-relaxed text-gray-700 dark:text-gray-200 pt-1.5" x-text="$store.confirm.message"></p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" @click="$store.confirm.cancel()"
                        class="px-4 py-2 rounded-xl text-sm font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    {{ __('Cancel') }}
                </button>
                <button type="button" @click="$store.confirm.accept()"
                        class="px-5 py-2 rounded-xl text-sm font-black text-white bg-brand-red-solid hover:shadow-[0_4px_15px_rgb(var(--brand-red-rgb)_/_0.4)] transition-all active:scale-95"
                        x-text="$store.confirm.confirmText"></button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('confirm', {
                show: false,
                message: '',
                confirmText: @js(__('Confirm')),
                _cb: null,
                // ask(message, onConfirm, confirmText?) — themed replacement for wire:confirm
                ask(message, onConfirm, confirmText) {
                    this.message = message;
                    this._cb = onConfirm;
                    this.confirmText = confirmText || @js(__('Confirm'));
                    this.show = true;
                },
                accept() { const cb = this._cb; this.show = false; this._cb = null; if (cb) cb(); },
                cancel() { this.show = false; this._cb = null; },
            });
        });
    </script>

    @stack('scripts')

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons()); document.addEventListener('livewire:navigated', () => lucide.createIcons());</script>
    {{-- Leaflet JS moved to the Contact page (@push) — see the note in <head>. --}}
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

        // Restart autoplay videos after a soft navigation. `autoplay` is a
        // one-shot attribute that only fires on the initial page parse, so the
        // hero background video goes blank once a wire:navigate morph runs
        // (e.g. switching language). Nudge each video back to life; reload only
        // if the morph severed its media source.
        document.addEventListener('livewire:navigated', () => {
            document.querySelectorAll('video[autoplay]').forEach((v) => {
                if (v.readyState === 0) v.load();
                v.play().catch(() => {});
            });
        });

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
            class="fixed bottom-6 left-1/2 -translate-x-1/2 sm:bottom-8 z-40 w-10 h-10 flex items-center justify-center rounded-full bg-brand-red text-white shadow-2xl transition-all duration-500 translate-y-20 opacity-0 pointer-events-none hover:bg-brand-red/90 hover:-translate-y-1 active:scale-95 focus:outline-none focus:ring-4 focus:ring-brand-red/30 group">
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
