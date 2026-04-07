<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Win Win Car Studio Accessories')</title>
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
        [x-cloak] { display: none !important; }
        .hero-gradient { background: linear-gradient(135deg, #111827 0%, #1f2937 50%, #DC2626 100%); }
        .nav-link { transition: color 0.2s; }
        .nav-link:hover { color: #FBBF24; }
    </style>
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

    <!-- Top Bar -->
    <div class="bg-brand-black text-xs text-gray-400 py-1.5">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <span>Free shipping on orders above RM150</span>
            <span>Call us: +60 12-345 6789</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-brand-red rounded-full flex items-center justify-center">
                        <span class="text-brand-yellow font-black text-lg">W</span>
                    </div>
                    <div class="leading-tight">
                        <div class="font-black text-brand-black text-sm uppercase tracking-wide">Win Win</div>
                        <div class="text-xs text-brand-red font-semibold uppercase tracking-widest">Car Studio</div>
                    </div>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="nav-link font-medium text-gray-700 {{ request()->routeIs('home') ? 'text-brand-red font-bold' : '' }}">Home</a>
                    <a href="{{ route('products') }}" class="nav-link font-medium text-gray-700 {{ request()->routeIs('products*') ? 'text-brand-red font-bold' : '' }}">Products</a>
                    <a href="{{ route('about') }}" class="nav-link font-medium text-gray-700 {{ request()->routeIs('about') ? 'text-brand-red font-bold' : '' }}">About Us</a>
                    <a href="{{ route('contact') }}" class="nav-link font-medium text-gray-700 {{ request()->routeIs('contact') ? 'text-brand-red font-bold' : '' }}">Contact</a>
                </div>

                <!-- CTA Button -->
                <a href="{{ route('products') }}" class="hidden md:inline-block bg-brand-red text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-red-700 transition">
                    Shop Now
                </a>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden p-2 text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4 space-y-2">
                <a href="{{ route('home') }}" class="block py-2 font-medium text-gray-700 hover:text-brand-red">Home</a>
                <a href="{{ route('products') }}" class="block py-2 font-medium text-gray-700 hover:text-brand-red">Products</a>
                <a href="{{ route('about') }}" class="block py-2 font-medium text-gray-700 hover:text-brand-red">About Us</a>
                <a href="{{ route('contact') }}" class="block py-2 font-medium text-gray-700 hover:text-brand-red">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 max-w-7xl mx-auto mt-4 rounded">
        {{ session('success') }}
    </div>
    @endif

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-brand-black text-gray-300 mt-16">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 bg-brand-red rounded-full flex items-center justify-center">
                            <span class="text-brand-yellow font-black text-lg">W</span>
                        </div>
                        <div>
                            <div class="font-black text-white text-lg">Win Win Car Studio</div>
                            <div class="text-xs text-brand-yellow uppercase tracking-widest">Accessories</div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Your one-stop shop for premium car accessories. We provide high-quality products to make your vehicle look and perform at its best.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-bold text-white mb-4 uppercase text-sm tracking-wider">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-brand-yellow transition">Home</a></li>
                        <li><a href="{{ route('products') }}" class="hover:text-brand-yellow transition">Products</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-brand-yellow transition">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-brand-yellow transition">Contact</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="font-bold text-white mb-4 uppercase text-sm tracking-wider">Contact Us</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start gap-2">
                            <span class="text-brand-yellow mt-0.5">📍</span>
                            <span>123 Jalan Auto, Kuala Lumpur, Malaysia</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-brand-yellow">📞</span>
                            <span>+60 12-345 6789</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-brand-yellow">✉️</span>
                            <span>info@winwincarstudio.com</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-brand-yellow">🕐</span>
                            <span>Mon–Sat: 9am – 7pm</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} Win Win Car Studio Accessories. All rights reserved.</p>
                <p class="mt-2 md:mt-0">Designed with ❤️ for car enthusiasts</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>

    @livewireScripts
</body>
</html>
