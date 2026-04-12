<div class="fixed z-50 bottom-6 right-6 font-sans"
     x-data="{ focused: false }"
     @keydown.escape.window="$wire.close()">

    {{-- Toggle Button --}}
    <button wire:click="{{ $isOpen ? 'close' : 'open' }}"
            class="w-14 h-14 bg-brand-red hover:bg-red-700 text-white rounded-full flex items-center justify-center shadow-2xl transition-transform hover:scale-105 active:scale-95 focus:outline-none focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900"
            aria-label="{{ $isOpen ? __('Close chat') : __('Open AI assistant') }}">
        @if($isOpen)
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        @else
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        @endif
    </button>

    {{-- Chat Window --}}
    @if($isOpen)
    <div class="absolute bottom-20 right-0 w-[350px] max-w-[calc(100vw-2rem)] h-[500px] max-h-[calc(100vh-8rem)] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden"
         style="transform-origin: bottom right;"
         wire:key="chatbot-window">

        {{-- Header --}}
        <div class="bg-gray-50 dark:bg-gray-900 px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 bg-brand-red rounded-full flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-gray-900 rounded-full"></span>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 dark:text-white text-sm">{{ __('Win Win AI Assistant') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Powered by Claude') }}</p>
                </div>
            </div>
            <button wire:click="clearChat"
                    class="text-xs text-gray-400 hover:text-brand-red transition-colors mr-2"
                    title="{{ __('Clear chat') }}">
                {{ __('Clear') }}
            </button>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50/50 dark:bg-gray-800/50"
             id="chatbot-messages"
             wire:key="messages-{{ count($messages) }}">

            @if(count($messages) === 0)
            <div class="flex justify-start">
                <div class="max-w-[85%] bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 border border-gray-100 dark:border-gray-600 rounded-2xl rounded-bl-none px-4 py-2.5 text-sm shadow-sm">
                    {{ __('Hi! I\'m the Win Win AI assistant. Ask me about our products, services, or opening hours!') }} 👋
                </div>
            </div>
            @endif

            @foreach($messages as $msg)
            <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[85%] rounded-2xl px-4 py-2.5 text-sm shadow-sm
                    {{ $msg['role'] === 'user'
                        ? 'bg-brand-red text-white rounded-br-none'
                        : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 border border-gray-100 dark:border-gray-600 rounded-bl-none' }}">
                    <span class="leading-relaxed whitespace-pre-wrap">{{ $msg['text'] }}</span>
                </div>
            </div>
            @endforeach

            @if($isLoading)
            <div class="flex justify-start">
                <div class="bg-white dark:bg-gray-700 border border-gray-100 dark:border-gray-600 rounded-2xl rounded-bl-none px-4 py-3 flex items-center gap-1 shadow-sm">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
            </div>
            @endif
        </div>

        {{-- Input --}}
        <div class="p-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <input wire:model="userInput"
                       wire:keydown.enter="sendMessage"
                       type="text"
                       id="chatbot-input"
                       placeholder="{{ __('Ask about products, services...') }}"
                       {{ $isLoading ? 'disabled' : '' }}
                       class="w-full bg-gray-100 dark:bg-gray-800 border-transparent focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-brand-red focus:border-transparent rounded-full px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 transition-all disabled:opacity-60">
                <button wire:click="sendMessage"
                        wire:loading.attr="disabled"
                        {{ strlen(trim($userInput)) === 0 || $isLoading ? 'disabled' : '' }}
                        class="bg-brand-red hover:bg-red-700 disabled:opacity-50 text-white p-2.5 rounded-full flex-shrink-0 transition-colors">
                    <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
            <p class="text-center text-[10px] text-gray-400 mt-2">{{ __('AI assistant — responses may not be 100% accurate') }}</p>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom on new messages
        (function () {
            var el = document.getElementById('chatbot-messages');
            if (el) el.scrollTop = el.scrollHeight;
        })();
    </script>
    @endif
</div>
