<div>
    {{-- ① Backdrop (mobile only, z-48) --}}
    @if($isOpen)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[48] md:hidden transition-opacity duration-300"
         wire:click="close"
         x-transition.opacity></div>
    @endif

    {{-- ② Chat Window (z-49) --}}
    @if($isOpen)
    <div class="fixed z-[49] flex flex-col overflow-hidden
                inset-x-0 bottom-0 h-[88vh] rounded-t-[2rem]
                md:inset-auto md:bottom-24 md:right-6 md:h-[600px] md:max-h-[85vh] md:w-[400px] md:rounded-3xl
                bg-white dark:bg-gray-900
                shadow-[0_20px_50px_-12px_rgba(0,0,0,0.3)] md:border border-gray-100 dark:border-gray-800"
         wire:key="chatbot-window"
         x-data
         @chatbot-scroll.window="$nextTick(() => { let el = document.getElementById('chatbot-messages'); if (el) el.scrollTop = el.scrollHeight; })"
         @keydown.escape.window="$wire.close()">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-red-600 to-red-800 px-5 py-4 flex items-center justify-between flex-shrink-0 shadow-sm relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay"></div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="relative group cursor-pointer">
                    <div class="w-11 h-11 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center ring-2 ring-white/30 shadow-inner group-hover:scale-105 transition-transform">
                        <span class="text-xl">🚗</span>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-red-700 rounded-full animate-pulse"></span>
                </div>
                <div>
                    <h3 class="font-bold text-white text-[15px] leading-tight tracking-wide">Win Win Assistant</h3>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                        <p class="text-red-100 text-xs font-medium">Online now</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 relative z-10">
                @if($chatLang !== '')
                <button wire:click="clearChat"
                        class="text-red-100 hover:text-white text-xs font-semibold px-2 py-1.5 rounded-lg hover:bg-white/10 transition-colors">
                    Clear
                </button>
                @endif
                <button wire:click="close"
                        class="text-red-100 hover:text-white transition-all duration-200 p-2 rounded-full hover:bg-white/20 active:scale-95"
                        aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Language Selection --}}
        @if($chatLang === '')
        <div class="flex-1 flex flex-col bg-white dark:bg-gray-900 overflow-hidden relative">
            <div class="absolute inset-0 bg-gray-50/50 dark:bg-gray-900/50 z-0"></div>
            
            <div class="bg-gradient-to-br from-red-700 to-red-600 px-6 py-8 text-center flex-shrink-0 rounded-b-[2rem] shadow-md relative overflow-hidden z-10">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-black/10 blur-xl"></div>
                
                <div class="relative z-10">
                    <div class="w-16 h-16 mx-auto bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mb-4 ring-1 ring-white/30 shadow-xl">
                        <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3.375 4.5C2.339 4.5 1.5 5.34 1.5 6.375V13.5h12V6.375c0-1.036-.84-1.875-1.875-1.875h-8.25zM13.5 15h-12v2.625c0 1.035.84 1.875 1.875 1.875h.375a3 3 0 116 0h3a.75.75 0 00.75-.75V15z"/>
                            <path d="M8.25 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0zM15.75 6.75a.75.75 0 00-.75.75v11.25c0 .087.015.17.042.248a3 3 0 015.958.464c.853-.175 1.522-.935 1.464-1.883a18.659 18.659 0 00-3.732-10.104 1.837 1.837 0 00-1.47-.725H15.75z"/>
                            <path d="M19.5 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z"/>
                        </svg>
                    </div>
                    <h4 class="text-white font-bold text-2xl mb-1 tracking-tight">Welcome!</h4>
                    <p class="text-red-100 text-sm font-medium">How can we assist you today?</p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-6 pt-6 pb-8 relative z-10">
                <div class="flex items-center justify-center gap-3 mb-6">
                    <div class="h-px bg-gray-200 dark:bg-gray-700 flex-1"></div>
                    <p class="text-center text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        Choose Language
                    </p>
                    <div class="h-px bg-gray-200 dark:bg-gray-700 flex-1"></div>
                </div>

                <div class="space-y-3.5">
                    @foreach([['en','🇬🇧','English','Continue in English'], ['ms','🇲🇾','Bahasa Melayu','Teruskan dalam BM'], ['zh','🇨🇳','中文','以中文继续']] as [$code, $flag, $label, $sub])
                    <button wire:click="selectLang('{{ $code }}')"
                            class="w-full flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/60
                                   hover:border-red-500/50 hover:shadow-[0_8px_30px_rgb(220,38,38,0.08)] hover:-translate-y-0.5
                                   active:scale-[0.98]
                                   transition-all duration-300 group text-left">
                        <div class="w-12 h-12 rounded-full bg-gray-50 dark:bg-gray-700/50 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform duration-300">
                            {{ $flag }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-800 dark:text-gray-100 text-[15px] group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">{{ $label }}</p>
                            <p class="text-gray-400 dark:text-gray-500 text-[13px] font-medium mt-0.5">{{ $sub }}</p>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-gray-50 dark:bg-gray-700/50 flex items-center justify-center group-hover:bg-red-50 dark:group-hover:bg-red-500/10 transition-colors">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Chat Messages --}}
        @else
        <div class="flex-1 overflow-y-auto px-4 py-5 space-y-4 bg-[#f8f9fa] dark:bg-gray-900 scroll-smooth"
             id="chatbot-messages">

            @foreach($messages as $msg)
            <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }} items-end gap-2 group">
                @if($msg['role'] !== 'user')
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center flex-shrink-0 mb-0.5 shadow-sm ring-2 ring-white dark:ring-gray-900">
                    <span class="text-sm">🚗</span>
                </div>
                @endif

                <div class="max-w-[78%] rounded-[1.25rem] px-4 py-3 shadow-sm relative
                    {{ $msg['role'] === 'user'
                        ? 'bg-gradient-to-br from-red-600 to-red-700 text-white rounded-br-sm'
                        : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border border-gray-100 dark:border-gray-700/60 rounded-bl-sm' }}">
                    <span class="leading-relaxed whitespace-pre-wrap text-[14px]">{{ $msg['text'] }}</span>
                </div>
            </div>
            @endforeach

            @if($isLoading)
            <div class="flex justify-start items-end gap-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center flex-shrink-0 shadow-sm ring-2 ring-white dark:ring-gray-900">
                    <span class="text-sm">🚗</span>
                </div>
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-[1.25rem] rounded-bl-sm px-4 py-3.5 flex items-center gap-1.5 shadow-sm">
                    <div class="w-2 h-2 bg-red-400 rounded-full animate-bounce" style="animation-delay:0ms"></div>
                    <div class="w-2 h-2 bg-red-400 rounded-full animate-bounce" style="animation-delay:150ms"></div>
                    <div class="w-2 h-2 bg-red-400 rounded-full animate-bounce" style="animation-delay:300ms"></div>
                </div>
            </div>
            @endif
        </div>

        {{-- Input Bar --}}
        <div class="p-4 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 flex-shrink-0">
            <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800/80 rounded-2xl px-2 py-2 ring-1 ring-gray-200 dark:ring-gray-700/60 focus-within:ring-2 focus-within:ring-red-500/50 focus-within:bg-white dark:focus-within:bg-gray-800 transition-all shadow-sm group">
                <input wire:model="userInput"
                       wire:keydown.enter="sendMessage"
                       wire:loading.attr="disabled"
                       wire:target="sendMessage"
                       type="text"
                       id="chatbot-input"
                       maxlength="500"
                       placeholder="{{ $chatLang === 'ms' ? 'Taip mesej...' : ($chatLang === 'zh' ? '输入消息...' : 'Type a message...') }}"
                       {{ $isLoading ? 'disabled' : '' }}
                       class="flex-1 bg-transparent border-none outline-none text-[15px] text-gray-800 dark:text-gray-200 placeholder-gray-400 disabled:opacity-50 focus:ring-0 px-3 py-1.5">
                
                <button wire:click="sendMessage"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage"
                        {{ $isLoading ? 'disabled' : '' }}
                        class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-all duration-300
                               bg-red-600 text-white shadow-md shadow-red-500/20
                               hover:bg-red-700 hover:shadow-lg hover:shadow-red-500/40 hover:-translate-y-0.5 active:scale-95
                               disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none
                               opacity-80 group-focus-within:opacity-100 cursor-pointer">
                    <svg class="w-5 h-5 ml-0.5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z"/>
                    </svg>
                </button>
            </div>
            <div class="text-center mt-2">
                <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Powered by AI · Responses may vary</span>
            </div>
        </div>
        @endif

    </div>
    @endif

    {{-- ③ Toggle Button (always on top, z-50) --}}
    <div class="fixed bottom-6 right-6 z-[50] md:bottom-8 md:right-8">
        <div class="relative group">
            @if(!$isOpen)
            <div class="absolute -inset-2 rounded-full bg-red-500 opacity-20 group-hover:opacity-40 blur-lg transition-opacity duration-500 animate-pulse"></div>
            @endif
            <button wire:click="{{ $isOpen ? 'close' : 'open' }}"
                    class="relative w-14 h-14 md:w-16 md:h-16 rounded-full flex items-center justify-center
                           bg-gradient-to-br from-red-600 to-red-800
                           shadow-[0_8px_30px_rgb(220,38,38,0.4)]
                           hover:shadow-[0_8px_40px_rgb(220,38,38,0.6)] hover:scale-110 active:scale-95
                           transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-red-500/30"
                    aria-label="{{ $isOpen ? 'Close chat' : 'Chat with us' }}">
                @if($isOpen)
                <svg class="w-6 h-6 md:w-7 md:h-7 text-white transform transition-transform duration-300 rotate-90 hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                @else
                <svg class="w-6 h-6 md:w-7 md:h-7 text-white transform transition-transform duration-300 group-hover:-rotate-12" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.804 21.644A6.707 6.707 0 006 21.75a6.721 6.721 0 003.583-1.029c.774.182 1.584.279 2.417.279 5.322 0 9.75-3.97 9.75-9 0-5.03-4.428-9-9.75-9s-9.75 3.97-9.75 9c0 2.409 1.025 4.587 2.674 6.192.232.226.277.428.254.543a3.73 3.73 0 01-.814 1.686.75.75 0 00.44 1.223 3.78 3.78 0 001.178-.471zM8.25 10.875a1.125 1.125 0 100 2.25 1.125 1.125 0 000-2.25zM10.875 12a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zm4.875-1.125a1.125 1.125 0 100 2.25 1.125 1.125 0 000-2.25z" clip-rule="evenodd"/>
                </svg>
                <span class="absolute top-1 right-1 w-3.5 h-3.5 md:w-4 md:h-4 bg-green-400 border-2 border-white rounded-full"></span>
                @endif
            </button>
        </div>
    </div>
</div>
