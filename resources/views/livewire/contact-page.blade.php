<div>
    <!-- Header -->
    <div class="bg-brand-black text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-5xl font-black mb-4">Contact <span class="text-brand-yellow">Us</span></h1>
            <p class="text-gray-400">We'd love to hear from you. Send us a message!</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

            <!-- Contact Info -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-2xl font-black text-brand-black mb-6">Get In Touch</h2>
                </div>

                @foreach([
                    ['📍', 'Address', '123 Jalan Auto, Taman Kereta, 50000 Kuala Lumpur, Malaysia'],
                    ['📞', 'Phone', '+60 12-345 6789'],
                    ['✉️', 'Email', 'info@winwincarstudio.com'],
                    ['🕐', 'Business Hours', 'Monday – Saturday: 9:00 AM – 7:00 PM'],
                ] as [$icon, $label, $value])
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-brand-red/10 text-brand-red rounded-xl flex items-center justify-center text-xl flex-shrink-0">
                        {{ $icon }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 mb-1">{{ $label }}</div>
                        <div class="text-gray-600 text-sm">{{ $value }}</div>
                    </div>
                </div>
                @endforeach

                <!-- Social Links -->
                <div class="pt-6 border-t border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-3">Follow Us</h3>
                    <div class="flex gap-3">
                        @foreach(['Facebook', 'Instagram', 'TikTok'] as $social)
                        <a href="#" class="w-10 h-10 bg-brand-red text-white rounded-full flex items-center justify-center text-xs font-bold hover:bg-red-700 transition">
                            {{ substr($social, 0, 2) }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h2 class="text-2xl font-black text-brand-black mb-6">Send a Message</h2>

                    @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-6">
                        {{ session('success') }}
                    </div>
                    @endif

                    <form wire:submit="submit" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name *</label>
                                <input wire:model="name" type="text" placeholder="Your name"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition">
                                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address *</label>
                                <input wire:model="email" type="email" placeholder="your@email.com"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition">
                                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number</label>
                                <input wire:model="phone" type="text" placeholder="+60 12-345 6789"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition">
                                @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Subject *</label>
                                <input wire:model="subject" type="text" placeholder="How can we help?"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition">
                                @error('subject') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Message *</label>
                            <textarea wire:model="message" rows="5" placeholder="Write your message here..."
                                      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition resize-none"></textarea>
                            @error('message') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit"
                                class="w-full bg-brand-red text-white py-4 rounded-xl font-bold text-lg hover:bg-red-700 transition"
                                wire:loading.attr="disabled" wire:loading.class="opacity-75">
                            <span wire:loading.remove>Send Message →</span>
                            <span wire:loading>Sending...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Placeholder -->
    <div class="bg-gray-200 h-64 flex items-center justify-center">
        <div class="text-center text-gray-500">
            <div class="text-5xl mb-2">📍</div>
            <p class="font-semibold">Win Win Car Studio Accessories</p>
            <p class="text-sm">123 Jalan Auto, Kuala Lumpur, Malaysia</p>
        </div>
    </div>
</div>
