@props(['position' => 'bottom-right'])

<div x-data="{
        isOpen: false,
        messages: [
            { type: 'ai', text: 'Hi there! I\'m your AI Mechanic. Is your car making a weird noise, or are you looking for a specific part fitment?' }
        ],
        newMessage: '',
        isTyping: false,
        sendMessage() {
            if (this.newMessage.trim() === '') return;
            
            // Add user message
            this.messages.push({ type: 'user', text: this.newMessage });
            const query = this.newMessage;
            this.newMessage = '';
            
            // Scroll to bottom
            this.$nextTick(() => { this.scrollToBottom(); });
            
            // Fake AI typing delay
            this.isTyping = true;
            setTimeout(() => {
                this.isTyping = false;
                this.messages.push({ 
                    type: 'ai', 
                    text: 'That sounds like it could be an issue with your brake pads or rotors. I recommend checking our high-performance brake kits! Would you like me to pull up parts that fit your vehicle?' 
                });
                this.$nextTick(() => { this.scrollToBottom(); });
            }, 1500);
        },
        scrollToBottom() {
            const container = this.$refs.chatContainer;
            container.scrollTop = container.scrollHeight;
        }
    }" 
    class="fixed z-50 {{ $position === 'bottom-right' ? 'bottom-6 right-6' : 'bottom-6 left-6' }} font-sans"
    @keydown.escape.window="isOpen = false">

    <!-- Chat Button Toggle -->
    <button @click="isOpen = !isOpen; if(isOpen) $nextTick(() => $refs.chatInput.focus())" 
            class="w-14 h-14 bg-brand-red hover:bg-red-700 text-white rounded-full flex items-center justify-center shadow-2xl transition-transform hover:scale-105 active:scale-95 focus:outline-none focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900"
            aria-label="Toggle AI Mechanic Chat">
        
        <svg x-show="!isOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <!-- Sparkles / Robot icon representation -->
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>

        <svg x-show="isOpen" style="display: none;" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Chat Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         style="display: none;"
         class="absolute bottom-20 right-0 w-[350px] max-w-[calc(100vw-3rem)] h-[500px] max-h-[calc(100vh-8rem)] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden transform origin-bottom-right">
        
        <!-- Header -->
        <div class="bg-gray-50 dark:bg-gray-900 px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 bg-brand-red rounded-full flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-gray-900 rounded-full"></span>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 dark:text-white text-md">AI Mechanic</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Powered by Local AI</p>
                </div>
            </div>
            <button @click="isOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div x-ref="chatContainer" class="flex-1 overflow-y-auto p-5 pb-2 space-y-4 bg-gray-50/50 dark:bg-gray-800/50">
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="['flex w-full', msg.type === 'user' ? 'justify-end' : 'justify-start']">
                    <div :class="[
                        'max-w-[80%] rounded-2xl px-4 py-2.5 text-sm shadow-sm',
                        msg.type === 'user' ? 'bg-brand-red text-white rounded-br-none' : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 border border-gray-100 dark:border-gray-600 rounded-bl-none'
                    ]">
                        <span x-text="msg.text" class="leading-relaxed whitespace-pre-wrap"></span>
                    </div>
                </div>
            </template>
            
            <!-- Typing Indicator -->
            <div x-show="isTyping" style="display: none;" class="flex w-full justify-start">
                <div class="bg-white dark:bg-gray-700 border border-gray-100 dark:border-gray-600 rounded-2xl rounded-bl-none px-4 py-3 flex items-center gap-1 shadow-sm">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
            <form @submit.prevent="sendMessage" class="flex items-center gap-2">
                <input x-ref="chatInput"
                       type="text" 
                       x-model="newMessage" 
                       placeholder="Ask about a car issue..." 
                       class="w-full bg-gray-100 dark:bg-gray-800 border-transparent focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-brand-red focus:border-transparent rounded-full px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 transition-all shadow-inner" />
                <button type="submit" 
                        :disabled="newMessage.trim() === '' || isTyping"
                        class="bg-brand-red hover:bg-red-700 disabled:opacity-50 disabled:hover:bg-brand-red text-white p-2.5 rounded-full flex-shrink-0 transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-brand-red dark:focus:ring-offset-gray-900">
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                </button>
            </form>
            <div class="text-center mt-2">
                 <span class="text-[10px] text-gray-400 font-medium">Free Mechanical AI - Capstone Project</span>
            </div>
        </div>
    </div>
</div>
