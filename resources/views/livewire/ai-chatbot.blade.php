{{-- Mobile backdrop --}}
@if($isOpen)
<div class="fixed inset-0 bg-black/40 z-40 md:hidden backdrop-blur-sm"
     wire:click="close"></div>
@endif

<div class="fixed z-50 bottom-6 right-6 font-sans"
     x-data="{}"
     @keydown.escape.window="$wire.close()"
     @chatbot-scroll.window="$nextTick(() => { let el = document.getElementById('chatbot-messages'); if (el) el.scrollTop = el.scrollHeight; })">

    {{-- Floating Toggle Button --}}
    <div class="relative">
        {{-- Pulse ring --}}
        @if(!$isOpen)
        <span class="absolute -inset-1.5 rounded-full bg-red-500 opacity-25 animate-pulse pointer-events-none"></span>
        @endif

        <button wire:click="{{ $isOpen ? 'close' : 'open' }}"
                class="relative w-16 h-16 rounded-full flex items-center justify-center
                       bg-gradient-to-br from-red-500 to-red-700
                       shadow-2xl shadow-red-600/50
                       hover:shadow-red-500/70 hover:scale-110 active:scale-95
                       transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-red-400/50"
                aria-label="{{ $isOpen ? __('Close chat') : __('Chat with us') }}">

            @if($isOpen)
            {{-- X icon --}}
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            @else
            {{-- Chat bubble icon --}}
            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M4.804 21.644A6.707 6.707 0 006 21.75a6.721 6.721 0 003.583-1.029c.774.182 1.584.279 2.417.279 5.322 0 9.75-3.97 9.75-9 0-5.03-4.428-9-9.75-9s-9.75 3.97-9.75 9c0 2.409 1.025 4.587 2.674 6.192.232.226.277.428.254.543a3.73 3.73 0 01-.814 1.686.75.75 0 00.44 1.223 3.78 3.78 0 001.178-.471zM8.25 10.875a1.125 1.125 0 100 2.25 1.125 1.125 0 000-2.25zM10.875 12a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zm4.875-1.125a1.125 1.125 0 100 2.25 1.125 1.125 0 000-2.25z" clip-rule="evenodd"/>
            </svg>
            {{-- Online badge --}}
            <span class="absolute top-0.5 right-0.5 w-4 h-4 bg-green-400 border-2 border-white rounded-full"></span>
            @endif
        </button>
    </div>

    {{-- Chat Window --}}
    @if($isOpen)
    <div class="fixed inset-x-0 bottom-0 h-[88vh]
                md:inset-auto md:absolute md:bottom-20 md:right-0 md:h-[540px] md:w-[390px]
                bg-white dark:bg-gray-900
                rounded-t-3xl md:rounded-2xl
                shadow-2xl overflow-hidden flex flex-col
                border border-gray-100 dark:border-gray-700/50"
         wire:key="chatbot-window">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-red-600 to-red-800 px-5 py-4 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                {{-- Avatar --}}
                <div class="relative">
                    <div class="w-11 h-11 bg-white/20 backdrop-blur rounded-full flex items-center justify-center ring-2 ring-white/30">
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3.375 4.5C2.339 4.5 1.5 5.34 1.5 6.375V13.5h12V6.375c0-1.036-.84-1.875-1.875-1.875h-8.25zM13.5 15h-12v2.625c0 1.035.84 1.875 1.875 1.875h.375a3 3 0 116 0h3a.75.75 0 00.75-.75V15z"/>
                            <path d="M8.25 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0zM15.75 6.75a.75.75 0 00-.75.75v11.25c0 .087.015.17.042.248a3 3 0 015.958.464c.853-.175 1.522-.935 1.464-1.883a18.659 18.659 0 00-3.732-10.104 1.837 1.837 0 00-1.47-.725H15.75z"/>
                            <path d="M19.5 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z"/>
                        </svg>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-red-700 rounded-full"></span>
                </div>
                <div>
                    <h3 class="font-bold text-white text-sm leading-tight">Win Win Assistant</h3>
                    <p class="text-red-200 text-xs">● Online now</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($chatLang !== '')
                <button wire:click="clearChat"
                        class="text-red-200 hover:text-white text-xs transition-colors"
                        title="{{ __('Clear chat') }}">
                    {{ __('Clear') }}
                </button>
                @endif
                <button wire:click="close"
                        class="text-red-200 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/10"
                        aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Language Selection ── --}}
        @if($chatLang === '')
        <div class="flex-1 flex flex-col bg-gray-50 dark:bg-gray-900 overflow-y-auto">

            {{-- Top banner --}}
            <div class="bg-gradient-to-b from-red-700 to-red-600 px-6 pt-6 pb-10 text-center">
                <div class="text-4xl mb-2">🚗</div>
                <h4 class="text-white font-bold text-lg">Welcome!</h4>
                <p class="text-red-200 text-sm mt-1">How can we help you today?</p>
            </div>

            {{-- Language cards --}}
            <div class="px-5 -mt-5 space-y-3">
                <p class="text-center text-xs text-gray-400 dark:text-gray-500 mb-4 mt-2">
                    Choose your language &nbsp;·&nbsp; Pilih bahasa &nbsp;·&nbsp; 选择语言
                </p>

                <button wire:click="selectLang('en')"
                        class="w-full flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                               hover:border-red-500 hover:shadow-lg hover:shadow-red-500/10 hover:-translate-y-0.5
                               transition-all duration-200 group">
                    <span class="text-3xl">🇬🇧</span>
                    <div class="text-left">
                        <p class="font-semibold text-gray-800 dark:text-white text-sm group-hover:text-red-600 transition-colors">English</p>
                        <p class="text-gray-400 text-xs">Continue in English</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-red-500 ml-auto transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <button wire:click="selectLang('ms')"
                        class="w-full flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                               hover:border-red-500 hover:shadow-lg hover:shadow-red-500/10 hover:-translate-y-0.5
                               transition-all duration-200 group">
                    <span class="text-3xl">🇲🇾</span>
                    <div class="text-left">
                        <p class="font-semibold text-gray-800 dark:text-white text-sm group-hover:text-red-600 transition-colors">Bahasa Melayu</p>
                        <p class="text-gray-400 text-xs">Teruskan dalam BM</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-red-500 ml-auto transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <button wire:click="selectLang('zh')"
                        class="w-full flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                               hover:border-red-500 hover:shadow-lg hover:shadow-red-500/10 hover:-translate-y-0.5
                               transition-all duration-200 group">
                    <span class="text-3xl">🇨🇳</span>
                    <div class="text-left">
                        <p class="font-semibold text-gray-800 dark:text-white text-sm group-hover:text-red-600 transition-colors">中文</p>
                        <p class="text-gray-400 text-xs">以中文继续</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-red-500 ml-auto transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Chat Screen ── --}}
        @else

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-3 bg-gray-50 dark:bg-gray-900/80"
             id="chatbot-messages">

            @foreach($messages as $index => $msg)
            <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }} items-end gap-2">

                {{-- Bot avatar --}}
                @if($msg['role'] !== 'user')
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center flex-shrink-0 mb-0.5 shadow">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3.375 4.5C2.339 4.5 1.5 5.34 1.5 6.375V13.5h12V6.375c0-1.036-.84-1.875-1.875-1.875h-8.25zM13.5 15h-12v2.625c0 1.035.84 1.875 1.875 1.875h.375a3 3 0 116 0h3a.75.75 0 00.75-.75V15z"/>
                        <path d="M8.25 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0zM15.75 6.75a.75.75 0 00-.75.75v11.25c0 .087.015.17.042.248a3 3 0 015.958.464c.853-.175 1.522-.935 1.464-1.883a18.659 18.659 0 00-3.732-10.104 1.837 1.837 0 00-1.47-.725H15.75z"/>
                        <path d="M19.5 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z"/>
                    </svg>
                </div>
                @endif

                <div class="max-w-[78%] rounded-2xl px-4 py-2.5 text-sm shadow-sm
                    {{ $msg['role'] === 'user'
                        ? 'bg-gradient-to-br from-red-500 to-red-700 text-white rounded-br-sm'
                        : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border border-gray-100 dark:border-gray-700 rounded-bl-sm' }}">
                    <span class="leading-relaxed whitespace-pre-wrap text-[13px]">{{ $msg['text'] }}</span>
                </div>
            </div>
            @endforeach

            @if($isLoading)
            <div class="flex justify-start items-end gap-2">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center flex-shrink-0 shadow">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3.375 4.5C2.339 4.5 1.5 5.34 1.5 6.375V13.5h12V6.375c0-1.036-.84-1.875-1.875-1.875h-8.25zM13.5 15h-12v2.625c0 1.035.84 1.875 1.875 1.875h.375a3 3 0 116 0h3a.75.75 0 00.75-.75V15z"/>
                        <path d="M8.25 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0zM15.75 6.75a.75.75 0 00-.75.75v11.25c0 .087.015.17.042.248a3 3 0 015.958.464c.853-.175 1.522-.935 1.464-1.883a18.659 18.659 0 00-3.732-10.104 1.837 1.837 0 00-1.47-.725H15.75z"/>
                        <path d="M19.5 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z"/>
                    </svg>
                </div>
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl rounded-bl-sm px-4 py-3 flex items-center gap-1.5 shadow-sm">
                    <div class="w-2 h-2 bg-red-400 rounded-full animate-bounce" style="animation-delay:0ms"></div>
                    <div class="w-2 h-2 bg-red-400 rounded-full animate-bounce" style="animation-delay:150ms"></div>
                    <div class="w-2 h-2 bg-red-400 rounded-full animate-bounce" style="animation-delay:300ms"></div>
                </div>
            </div>
            @endif
        </div>

        {{-- Input Bar --}}
        <div class="px-4 py-3 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 flex-shrink-0">
            <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 rounded-2xl px-4 py-2 focus-within:ring-2 focus-within:ring-red-500/50 transition-all">
                <input wire:model="userInput"
                       wire:keydown.enter="sendMessage"
                       wire:loading.attr="disabled"
                       wire:target="sendMessage"
                       type="text"
                       id="chatbot-input"
                       maxlength="500"
                       placeholder="{{ $chatLang === 'ms' ? 'Taip mesej...' : ($chatLang === 'zh' ? '输入消息...' : 'Type a message...') }}"
                       {{ $isLoading ? 'disabled' : '' }}
                       class="flex-1 bg-transparent border-none outline-none text-sm text-gray-800 dark:text-gray-200 placeholder-gray-400 disabled:opacity-50 focus:ring-0 p-0">
                <button wire:click="sendMessage"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage"
                        {{ strlen(trim($userInput)) === 0 || $isLoading ? 'disabled' : '' }}
                        class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-700 hover:from-red-600 hover:to-red-800
                               disabled:opacity-40 disabled:cursor-not-allowed
                               text-white rounded-xl flex items-center justify-center flex-shrink-0
                               transition-all active:scale-90">
                    {{-- Paper-plane send icon --}}
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z"/>
                    </svg>
                </button>
            </div>
        </div>

        @endif
    </div>
    @endif
</div>
