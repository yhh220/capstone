<div>
    <div class="min-h-screen flex items-center justify-center bg-gray-950 relative overflow-hidden">

        {{-- Animated Background Grid --}}
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0"
                style="background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px); background-size: 50px 50px;">
            </div>
        </div>

        {{-- Glow Effects --}}
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-red-600/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-yellow-500/5 rounded-full blur-3xl animate-pulse"
            style="animation-delay: 1s;"></div>

        <div class="relative z-10 w-full max-w-md px-4">

            {{-- Card --}}
            <div
                class="bg-gray-900/80 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-800/50 overflow-hidden">

                {{-- Header --}}
                <div class="p-8 text-center border-b border-gray-800/50">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-red-600 to-red-800 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-red-900/30 rotate-3 hover:rotate-0 transition-transform duration-300">
                        <span class="text-brand-yellow font-black text-3xl">W</span>
                    </div>
                    <h1 class="text-2xl font-black text-white mb-1">Secure Admin Portal</h1>
                    <p class="text-gray-500 text-sm">Win Win Car Studio • Authorized Personnel Only</p>

                    {{-- Security Status --}}
                    <div class="flex items-center justify-center gap-2 mt-4">
                        <span
                            class="flex items-center gap-1.5 text-xs text-green-500 bg-green-500/10 px-3 py-1 rounded-full border border-green-500/20">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                            Secure Connection
                        </span>
                    </div>
                </div>

                {{-- Form --}}
                <form wire:submit="login" class="p-8">

                    {{-- Error Flash --}}
                    @if(session('error'))
                        <div
                            class="mb-6 bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                            <span>🚫</span> {{ session('error') }}
                        </div>
                    @endif

                    {{-- Email --}}
                    <div class="mb-5">
                        <label for="admin-email"
                            class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Email
                            Address</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </span>
                            <input wire:model="email" type="email" id="admin-email" placeholder="admin@winwincar.com"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-white placeholder-gray-600 focus:ring-2 focus:ring-red-500/50 focus:border-red-500/50 outline-none transition text-sm"
                                autocomplete="email">
                        </div>
                        @error('email')
                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                                <span>⚠️</span> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-6">
                        <label for="admin-password"
                            class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <input wire:model="password" type="{{ $showPassword ? 'text' : 'password' }}"
                                id="admin-password" placeholder="••••••••••••"
                                class="w-full pl-10 pr-12 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-white placeholder-gray-600 focus:ring-2 focus:ring-red-500/50 focus:border-red-500/50 outline-none transition text-sm"
                                autocomplete="current-password">
                            <button type="button" wire:click="$toggle('showPassword')"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-400 transition">
                                @if($showPassword)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                @endif
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                                <span>⚠️</span> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit" id="btn-admin-login"
                        class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white py-3.5 rounded-xl font-bold text-sm uppercase tracking-wider hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-lg shadow-red-900/30 hover:shadow-red-900/50 flex items-center justify-center gap-2 disabled:opacity-50"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="login" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Authenticate
                        </span>
                        <span wire:loading wire:target="login" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                    fill="none" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            Verifying...
                        </span>
                    </button>
                </form>

                {{-- Footer --}}
                <div class="px-8 py-4 border-t border-gray-800/50 bg-gray-900/50">
                    <div class="flex items-center justify-between text-xs text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            bcrypt • 12 rounds
                        </span>
                        <span>Session protected</span>
                    </div>
                </div>

            </div>

            {{-- Back Link --}}
            <div class="text-center mt-6">
                <a href="{{ route('home') }}"
                    class="text-xs text-gray-600 hover:text-gray-400 transition flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Website
                </a>
            </div>

        </div>
    </div>
</div>