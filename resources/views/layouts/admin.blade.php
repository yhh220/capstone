<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Win Win Car Studio</title>
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
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans">

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-brand-black text-gray-200 flex flex-col flex-shrink-0">
        <!-- Logo -->
        <div class="px-6 py-5 border-b border-gray-700">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 bg-brand-red rounded-full flex items-center justify-center">
                    <span class="text-brand-yellow font-black">W</span>
                </div>
                <div class="leading-tight">
                    <div class="font-bold text-white text-sm">Win Win Admin</div>
                    <div class="text-xs text-brand-yellow">Car Studio</div>
                </div>
            </div>
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-3 px-2">Main</p>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-brand-red text-white' : 'hover:bg-gray-700' }} transition">
                <span>📊</span> Dashboard
            </a>

            <p class="text-xs text-gray-500 uppercase tracking-widest mt-5 mb-3 px-2">Catalog</p>
            <a href="{{ route('admin.products') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.products') ? 'bg-brand-red text-white' : 'hover:bg-gray-700' }} transition">
                <span>🚗</span> Products
            </a>
            <a href="{{ route('admin.categories') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.categories') ? 'bg-brand-red text-white' : 'hover:bg-gray-700' }} transition">
                <span>📂</span> Categories
            </a>

            <p class="text-xs text-gray-500 uppercase tracking-widest mt-5 mb-3 px-2">Operations</p>
            <a href="{{ route('admin.orders') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.orders') ? 'bg-brand-red text-white' : 'hover:bg-gray-700' }} transition">
                <span>📦</span> Orders
            </a>
            <a href="{{ route('admin.contacts') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.contacts') ? 'bg-brand-red text-white' : 'hover:bg-gray-700' }} transition">
                <span>✉️</span> Messages
            </a>
        </nav>

        <!-- Bottom -->
        <div class="px-4 py-4 border-t border-gray-700">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 text-sm transition text-gray-400">
                <span>🌐</span> View Website
            </a>
        </div>
    </aside>

    <!-- Main Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <span class="w-8 h-8 bg-brand-red text-white rounded-full flex items-center justify-center font-bold">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </span>
                <span>{{ Auth::user()->name ?? 'Admin' }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="ml-2 text-gray-400 hover:text-red-500 transition text-xs font-semibold uppercase">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
