@php
    // ── Lucide icon path map (lucide.dev, MIT) ─────────────────────────
    $lucide = [
        'car'           => '<path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/>',
        'x'             => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
        'send'          => '<path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/>',
        'reset'         => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>',
        'languages'     => '<path d="m5 8 6 6"/><path d="m4 14 6-6 2-3"/><path d="M2 5h12"/><path d="M7 2h1"/><path d="m22 22-5-10-5 10"/><path d="M14 18h6"/>',
        'sparkles'      => '<path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/><path d="M20 3v4"/><path d="M22 5h-4"/><path d="M4 17v2"/><path d="M5 18H3"/>',
        'arrow-right'   => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
        'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
        'message'       => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>',
        'check'         => '<path d="M20 6 9 17l-5-5"/>',
        'calendar'      => '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>',
        'package'       => '<path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
        'clock'         => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'map-pin'       => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
        'volume-2'      => '<path d="M11 4.702a.705.705 0 0 0-1.203-.498L6.413 7.587A1.4 1.4 0 0 1 5.416 8H3a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h2.416a1.4 1.4 0 0 1 .997.413l3.383 3.384A.705.705 0 0 0 11 19.298z"/><path d="M16 9a5 5 0 0 1 0 6"/><path d="M19.364 18.364a9 9 0 0 0 0-12.728"/>',
        'banknote'      => '<rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>',
    ];

    $icon = function (string $name, string $classes = 'w-5 h-5') use ($lucide) {
        $paths = $lucide[$name] ?? '';
        return '<svg class="' . $classes . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
    };

    $waUrl = 'https://wa.me/' . config('services.store.phone_raw') . '?text=' . rawurlencode('Hi Win Win Car Studio!');
    $ui = $this->ui;
    $langLabels = ['en' => 'EN', 'ms' => 'BM', 'zh' => '中文'];

    // Escape bot text, then turn phone numbers into tappable WhatsApp links.
    $formatBot = function (string $text) use ($waUrl) {
        $safe = e($text);
        return preg_replace_callback('/(\d{3}-\d{3,4}[\s-]?\d{3,4})/u', function ($m) use ($waUrl) {
            return '<a href="' . $waUrl . '" target="_blank" rel="noopener noreferrer" class="underline decoration-dotted underline-offset-2 font-semibold hover:opacity-80">' . $m[1] . '</a>';
        }, $safe);
    };
@endphp

<div>
    {{-- ① Backdrop (mobile only) --}}
    @if($isOpen)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9998] md:hidden" wire:click="close"></div>
    @endif

    {{-- ② Chat Window --}}
    @if($isOpen)
    <div class="chatbot-window fixed z-[9999] flex flex-col overflow-hidden
                inset-x-0 bottom-0 h-[90vh] rounded-t-[1.75rem]
                md:inset-auto md:bottom-24 md:right-6 md:h-[620px] md:max-h-[85vh] md:w-[400px] md:rounded-[1.75rem]
                bg-white dark:bg-gray-900
                shadow-[0_24px_60px_-12px_rgba(0,0,0,0.4)] md:border border-gray-100 dark:border-gray-800
                animate-[chatIn_0.3s_cubic-bezier(0.22,1,0.36,1)]"
         wire:key="chatbot-window"
         role="dialog"
         aria-modal="true"
         aria-label="{{ $ui['title'] }}"
         x-data="{ langOpen: false }"
         x-init="$nextTick(() => { let m = document.getElementById('chatbot-messages'); if (m) m.scrollTop = m.scrollHeight; if (window.matchMedia('(min-width: 768px)').matches) document.getElementById('chatbot-input')?.focus() })"
         @chatbot-scroll.window="$nextTick(() => { let el = document.getElementById('chatbot-messages'); if (el) el.scrollTop = el.scrollHeight; })"
         @chatbot-focus.window="$nextTick(() => document.getElementById('chatbot-input')?.focus())"
         @keydown.escape.window="$wire.close()">

        {{-- ── Header ── (z-30 so the language dropdown layers above the message list) --}}
        <div class="relative z-30 flex items-center justify-between gap-2 px-4 py-3.5 flex-shrink-0
                    bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700/60">

            <div class="flex items-center gap-3 relative z-10 min-w-0">
                <div class="w-10 h-10 flex-shrink-0 rounded-xl bg-brand-red text-white flex items-center justify-center">
                    {!! $icon('car', 'w-6 h-6') !!}
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-brand-black dark:text-white text-[15px] leading-tight truncate">{{ $ui['title'] }}</h3>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        <p class="text-gray-500 dark:text-gray-400 text-[11px] font-medium">{{ $ui['online'] }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1 relative z-10 flex-shrink-0">
                {{-- Language switcher --}}
                <div class="relative" @click.outside="langOpen = false">
                    <button @click="langOpen = !langOpen"
                            class="flex items-center gap-1 text-gray-500 dark:text-gray-400 hover:text-brand-red text-xs font-bold px-2 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                            aria-label="{{ $ui['lang'] }}">
                        {!! $icon('languages', 'w-4 h-4') !!}
                        <span>{{ $langLabels[$chatLang] }}</span>
                        {!! $icon('chevron-down', 'w-3 h-3') !!}
                    </button>
                    <div x-show="langOpen" x-cloak x-transition.origin.top.right
                         class="absolute right-0 top-full mt-1.5 w-36 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-20">
                        @foreach(['en' => 'English', 'ms' => 'Bahasa Melayu', 'zh' => '中文'] as $code => $name)
                        <button wire:click="switchLang('{{ $code }}')" @click="langOpen = false"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-left text-sm transition-colors
                                       {{ $chatLang === $code ? 'text-brand-red font-bold bg-red-50 dark:bg-red-500/10' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/60' }}">
                            <span>{{ $name }}</span>
                            @if($chatLang === $code) {!! $icon('check', 'w-4 h-4') !!} @endif
                        </button>
                        @endforeach
                    </div>
                </div>

                <button wire:click="clearChat"
                        class="text-gray-500 dark:text-gray-400 hover:text-brand-red p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 hover:rotate-[-45deg] transition-all duration-300"
                        aria-label="{{ $ui['clear'] }}" title="{{ $ui['clear'] }}">
                    {!! $icon('reset', 'w-[18px] h-[18px]') !!}
                </button>
                <button wire:click="close"
                        class="text-gray-500 dark:text-gray-400 hover:text-brand-red p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 hover:rotate-90 transition-all duration-300 active:scale-90"
                        aria-label="{{ $ui['close'] }}"
                        title="{{ $ui['close'] }}">
                    {!! $icon('x', 'w-5 h-5') !!}
                </button>
            </div>
        </div>

        {{-- ── Messages ── --}}
        <div class="flex-1 overflow-y-auto px-4 py-5 space-y-4 bg-gray-50 dark:bg-gray-900/60 scroll-smooth"
             id="chatbot-messages"
             role="log"
             aria-live="polite"
             aria-atomic="false">

            @foreach($messages as $msg)
            <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }} items-end gap-2">
                @if($msg['role'] !== 'user')
                <div class="w-8 h-8 rounded-xl bg-brand-red/10 text-brand-red flex items-center justify-center flex-shrink-0 border border-brand-red/20">
                    {!! $icon('car', 'w-[18px] h-[18px]') !!}
                </div>
                @endif

                <div class="max-w-[80%] flex flex-col {{ $msg['role'] === 'user' ? 'items-end' : 'items-start' }}">
                    <div class="rounded-2xl px-4 py-2.5 shadow-sm
                        {{ $msg['role'] === 'user'
                            ? 'bg-brand-red text-white rounded-br-md'
                            : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-700/60 rounded-bl-md' }}">
                        <span class="leading-relaxed whitespace-pre-wrap text-[14px]">{!! $msg['role'] === 'user' ? e($msg['text']) : $formatBot($msg['text']) !!}</span>
                    </div>

                    {{-- Page redirect CTA (itshover: lift + arrow nudge) --}}
                    @if(!empty($msg['cta']))
                    {{-- Full-page navigation (no wire:navigate) — matches the rest of the
                         site and lets the <head> theme script re-apply dark mode cleanly. --}}
                    <a href="{{ $msg['cta']['url'] }}"
                       class="group mt-2 inline-flex items-center gap-2 bg-brand-red text-white text-[13px] font-bold pl-4 pr-3 py-2 rounded-xl shadow-sm
                              hover:shadow-[0_6px_18px_rgb(var(--brand-red-rgb)_/_0.4)] hover:-translate-y-0.5 active:scale-95 transition-all duration-300">
                        <span>{{ $msg['cta']['label'] }}</span>
                        {!! $icon('arrow-right', 'w-4 h-4 transition-transform duration-300 group-hover:translate-x-1') !!}
                    </a>
                    @endif

                    {{-- "Did you mean…" / follow-up chips --}}
                    @if(!empty($msg['suggest']))
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @foreach($msg['suggest'] as $sug)
                        <button wire:click="quickAsk('{{ addslashes($sug['query']) }}')"
                                wire:loading.attr="disabled"
                                wire:target="sendMessage,quickAsk,generateReply"
                                class="text-[12px] font-semibold px-3 py-1.5 rounded-full border border-brand-red/30 text-brand-red bg-brand-red/5
                                       hover:bg-brand-red hover:text-white hover:border-brand-red active:scale-95 transition-all duration-300 disabled:opacity-50">
                            {{ $sug['label'] }}
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach

            @if($isLoading)
            <div class="flex justify-start items-end gap-2">
                <div class="w-8 h-8 rounded-xl bg-brand-red/10 text-brand-red flex items-center justify-center flex-shrink-0 border border-brand-red/20">
                    {!! $icon('car', 'w-[18px] h-[18px]') !!}
                </div>
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-2xl rounded-bl-md px-4 py-3 flex items-center gap-1.5 shadow-sm">
                    <span class="w-2 h-2 bg-brand-red/70 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 bg-brand-red/70 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 bg-brand-red/70 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
            </div>
            @endif
        </div>

        {{-- ── Suggestion chips ── --}}
        <div class="px-3 pt-2.5 pb-1 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 flex-shrink-0">
            <div class="flex items-center gap-1.5 mb-1 px-1">
                {!! $icon('sparkles', 'w-3 h-3 text-brand-red') !!}
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ $ui['quick'] }}</span>
            </div>
            {{-- pt/pb give the hover lift room; drag-to-scroll works on desktop & touch --}}
            <div x-data="{
                    isDown: false, startX: 0, scrollLeft: 0, hasDragged: false,
                    start(e) { this.isDown = true; this.hasDragged = false; this.startX = e.pageX - this.$el.offsetLeft; this.scrollLeft = this.$el.scrollLeft; },
                    stop()   { this.isDown = false; },
                    move(e)  { if (!this.isDown) return; e.preventDefault(); const walk = (e.pageX - this.$el.offsetLeft - this.startX) * 1.5; if (Math.abs(walk) > 4) this.hasDragged = true; this.$el.scrollLeft = this.scrollLeft - walk; }
                 }"
                 @mousedown="start($event)"
                 @mouseleave="stop()"
                 @mouseup="stop()"
                 @mousemove="move($event)"
                 @click.capture="if (hasDragged) { $event.stopPropagation(); $event.preventDefault(); hasDragged = false; }"
                 class="flex gap-2 overflow-x-auto scrollbar-hide pt-1.5 pb-2 -mx-1 px-1 touch-pan-x cursor-grab active:cursor-grabbing select-none">
                @foreach($this->suggestions as $chip)
                <button wire:click="quickAsk('{{ addslashes($chip['query']) }}')"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage,quickAsk,generateReply"
                        @disabled($isLoading)
                        class="group flex items-center gap-1.5 flex-shrink-0 whitespace-nowrap text-[12.5px] font-semibold
                               px-3 py-1.5 rounded-full border border-gray-200 dark:border-gray-700
                               text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/60
                               hover:border-brand-red hover:text-brand-red hover:bg-red-50 dark:hover:bg-red-500/10
                               hover:-translate-y-0.5 active:scale-95 transition-all duration-300 disabled:opacity-50">
                    <span class="text-brand-red/80 group-hover:text-brand-red transition-colors">{!! $icon($chip['icon'], 'w-3.5 h-3.5') !!}</span>
                    {{ $chip['label'] }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- ── Input bar ── --}}
        <div class="px-4 pb-3 pt-1 bg-white dark:bg-gray-900 flex-shrink-0">
            <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800/80 rounded-2xl px-2 py-1.5 ring-1 ring-gray-200 dark:ring-gray-700/60 focus-within:ring-2 focus-within:ring-brand-red/50 focus-within:bg-white dark:focus-within:bg-gray-800 transition-all">
                {{-- Input stays enabled so it keeps focus between messages; sendMessage()
                     guards against sending while a reply is still being generated. --}}
                <input wire:model="userInput"
                       wire:keydown.enter="sendMessage"
                       type="text"
                       id="chatbot-input"
                       maxlength="500"
                       autocomplete="off"
                       placeholder="{{ $ui['placeholder'] }}"
                       class="flex-1 bg-transparent border-none outline-none text-[15px] text-gray-800 dark:text-gray-200 placeholder-gray-400 disabled:opacity-50 focus:ring-0 focus-visible:outline-none px-2.5 py-1.5">

                <button wire:click="sendMessage"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage,quickAsk,generateReply"
                        @disabled($isLoading)
                        class="group w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                               bg-brand-red text-white shadow-md shadow-red-500/20
                               hover:bg-red-700 hover:shadow-lg hover:shadow-red-500/40 hover:-translate-y-0.5 active:scale-95
                               disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300"
                        aria-label="{{ $ui['send'] }}">
                    <span wire:loading.remove wire:target="sendMessage,quickAsk">
                        {!! $icon('send', 'w-[18px] h-[18px] transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5') !!}
                    </span>
                    <span wire:loading wire:target="sendMessage,quickAsk">
                        {!! $icon('reset', 'w-[18px] h-[18px] animate-spin') !!}
                    </span>
                </button>
            </div>

            <div class="flex items-center justify-between mt-2 px-1">
                <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">{{ $ui['powered'] }}</span>
                <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
                   class="group inline-flex items-center gap-1 text-[11px] font-bold text-[#25D366] hover:text-[#1da851] transition-colors">
                    <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:scale-110" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    {{ $ui['wa'] }}
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- ③ Floating toggle button (itshover: shine sweep; the online dot does the glowing) --}}
    {{-- Hidden on mobile while open (the in-window header handles closing there) --}}
    <div class="fixed bottom-6 right-6 z-[9998] md:bottom-8 md:right-8 {{ $isOpen ? 'hidden md:block' : '' }}">
        <div class="relative group">
            <button wire:click="{{ $isOpen ? 'close' : 'open' }}"
                    class="relative w-14 h-14 md:w-16 md:h-16 rounded-full flex items-center justify-center overflow-hidden
                           bg-brand-red text-white
                           shadow-[0_8px_30px_rgb(var(--brand-red-rgb)_/_0.45)]
                           hover:shadow-[0_8px_40px_rgb(var(--brand-red-rgb)_/_0.65)] hover:scale-110 active:scale-95
                           transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-brand-red/30"
                    aria-label="{{ $isOpen ? $ui['close'] : $ui['open'] }}">
                {{-- shine sweep --}}
                <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700" aria-hidden="true"></span>
                @if($isOpen)
                <span class="relative">{!! $icon('x', 'w-6 h-6 md:w-7 md:h-7 transition-transform duration-300 group-hover:rotate-90') !!}</span>
                @else
                <span class="relative">{!! $icon('car', 'w-7 h-7 md:w-8 md:h-8 transition-transform duration-300 group-hover:-rotate-6') !!}</span>
                @endif
            </button>
            {{-- Online dot — sibling of the button so the button's overflow-hidden can't clip it.
                 The dot itself glows via an expanding ping ring. --}}
            @if(!$isOpen)
            <span class="absolute top-0.5 right-0.5 md:top-1 md:right-1 flex h-3.5 w-3.5 md:h-4 md:w-4 pointer-events-none">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex h-3.5 w-3.5 md:h-4 md:w-4 rounded-full bg-green-400 border-2 border-white dark:border-gray-900"></span>
            </span>
            @endif
        </div>
    </div>

    @once
    @push('styles')
    <style>
        @keyframes chatIn {
            from { opacity: 0; transform: translateY(20px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        #chatbot-messages::-webkit-scrollbar { width: 5px; }
        #chatbot-messages::-webkit-scrollbar-thumb { background: rgb(var(--brand-red-rgb) / 0.3); border-radius: 9999px; }
    </style>
    @endpush
    @endonce
</div>
