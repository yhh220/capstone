<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    $storeName = config('services.store.name');
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') - {{ $storeName }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
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
        *:focus-visible {
            outline: 3px solid #DC2626;
            outline-offset: 2px;
            border-radius: 4px;
        }

        #admin-sidebar {
            transition: transform 0.25s cubic-bezier(.4, 0, .2, 1);
        }

        @media (prefers-reduced-motion: reduce) {
            #admin-sidebar {
                transition: none;
            }
        }
    </style>

    @livewireStyles
</head>
<body class="bg-gray-100 font-sans antialiased" id="admin-body">
<div class="flex h-screen overflow-hidden">
    <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/50 z-30 md:hidden" aria-hidden="true"></div>

    <aside id="admin-sidebar"
           class="fixed md:relative -translate-x-full md:translate-x-0 z-40 w-72 md:w-64 h-full bg-brand-black text-gray-200 flex flex-col flex-shrink-0 shadow-2xl md:shadow-none"
           role="navigation"
           aria-label="Admin navigation">
        <div class="px-6 py-5 border-b border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 bg-brand-red rounded-full flex items-center justify-center flex-shrink-0" aria-hidden="true">
                    <span class="text-brand-yellow font-black">W</span>
                </div>
                <div class="leading-tight">
                    <div class="font-bold text-white text-sm">Content Admin</div>
                    <div class="text-xs text-brand-yellow">{{ $storeName }}</div>
                </div>
            </div>
            <button id="sidebar-close"
                    class="md:hidden p-1.5 rounded-lg hover:bg-gray-700 transition text-gray-400 hover:text-white"
                    aria-label="Close navigation">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-0.5 overflow-y-auto" aria-label="Admin menu">
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-2 px-2 mt-1">Overview</p>
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-brand-red text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <span aria-hidden="true">📊</span> Dashboard
            </a>

            <p class="text-xs text-gray-500 uppercase tracking-widest mt-5 mb-2 px-2">Content</p>
            <a href="{{ route('admin.products') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.products') ? 'bg-brand-red text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <span aria-hidden="true">🚗</span> Products
            </a>
            <a href="{{ route('admin.categories') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.categories') ? 'bg-brand-red text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <span aria-hidden="true">📂</span> Categories
            </a>
            <a href="{{ route('admin.feedback') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.feedback') ? 'bg-brand-red text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <span aria-hidden="true">⭐</span> Feedback
            </a>

            <p class="text-xs text-gray-500 uppercase tracking-widest mt-5 mb-2 px-2">Leads</p>
            <a href="{{ route('admin.contacts') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.contacts') ? 'bg-brand-red text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <span aria-hidden="true">✉</span> Messages
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-gray-700 space-y-1">
            <a href="{{ route('home') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-400 hover:bg-gray-700 hover:text-white transition-colors"
               target="_blank"
               rel="noopener">
                <span aria-hidden="true">🌐</span> View Website
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="bg-white shadow-sm px-4 md:px-6 py-3 flex items-center justify-between gap-4 flex-shrink-0">
            <div class="flex items-center gap-3">
                <button id="sidebar-open"
                        class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
                        aria-label="Open navigation menu"
                        aria-expanded="false"
                        aria-controls="admin-sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-lg font-bold text-gray-800 truncate">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-2 text-sm text-gray-600 flex-shrink-0">
                <div class="w-8 h-8 bg-brand-red text-white rounded-full flex items-center justify-center font-bold text-sm" aria-hidden="true">A</div>
                <span class="hidden sm:inline">Admin</span>
            </div>
        </header>

        @if(session('success'))
        <div class="mx-4 md:mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm" role="alert" aria-live="polite">
            {{ session('success') }}
        </div>
        @endif

        <main class="flex-1 overflow-y-auto p-4 md:p-6" id="admin-main">
            {{ $slot }}
        </main>
    </div>
</div>

<script>
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const btnOpen = document.getElementById('sidebar-open');
    const btnClose = document.getElementById('sidebar-close');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        btnOpen.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        btnOpen.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    btnOpen?.addEventListener('click', openSidebar);
    btnClose?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });
</script>

@livewireScripts
</body>
</html>
