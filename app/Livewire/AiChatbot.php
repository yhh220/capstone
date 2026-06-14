<?php

namespace App\Livewire;

use App\Contracts\AiServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class AiChatbot extends Component
{
    // #[Locked] = server-controlled only; the client cannot tamper with these
    // via the Livewire update endpoint. Only $userInput is client-writable
    // (wire:model), and it runs through the full sanitisation pipeline.
    #[Locked]
    public bool $isOpen = false;

    public string $userInput = '';

    #[Locked]
    public array $messages = [];

    #[Locked]
    public bool $isLoading = false;

    #[Locked]
    public string $chatLang = 'en';

    /** The user message awaiting an AI reply (drives the two-step typing indicator). */
    #[Locked]
    public ?string $pendingText = null;

    /** Route name of the page the chatbot is embedded on (captured at mount). */
    #[Locked]
    public string $currentRoute = '';

    private const MAX_INPUT_LENGTH = 500;
    private const MAX_HISTORY = 20;
    private const MAX_MESSAGES = 40;        // hard cap on retained transcript
    private const RATE_LIMIT_MAX = 12;      // messages per minute / IP
    private const RATE_LIMIT_DECAY = 60;
    private const HOURLY_MAX = 120;         // messages per hour / IP (sustained-spam guard)

    // Abuse / security thresholds
    private const ABUSE_BLOCK_THRESHOLD = 4;   // malicious attempts before a cooldown
    private const ABUSE_BLOCK_SECONDS = 600;   // 10-minute cooldown

    private function ai(): AiServiceInterface
    {
        return app(AiServiceInterface::class);
    }

    public function mount(): void
    {
        // The chat keeps its OWN language, independent of the site. It seeds
        // from the site locale on first use, but once the visitor picks a chat
        // language it sticks (persisted in the chatbot session) even if they
        // switch the whole site to another language.
        $saved = session('chatbot');
        $savedLang = is_array($saved) ? ($saved['lang'] ?? null) : null;
        if (in_array($savedLang, ['en', 'ms', 'zh'], true)) {
            $this->chatLang = $savedLang;
        } else {
            $locale = app()->getLocale();
            $this->chatLang = in_array($locale, ['en', 'ms', 'zh'], true) ? $locale : 'en';
        }

        // Capture the current page route now — during later chat AJAX requests
        // request()->route() resolves to the Livewire update endpoint, not the page.
        $this->currentRoute = request()->route()?->getName() ?? '';

        // Restore an in-progress conversation so it survives page navigation.
        if (is_array($saved) && ! empty($saved['messages'])) {
            $this->messages = $saved['messages'];
            $this->isOpen = (bool) ($saved['open'] ?? false);
        } else {
            $this->pushGreeting();
        }
    }

    /**
     * Persist the conversation + open state so it survives full-page navigation.
     */
    private function persist(): void
    {
        // Hard-cap the transcript in memory and in the session so a long-lived
        // chat can't bloat memory or session storage.
        if (count($this->messages) > self::MAX_MESSAGES) {
            $this->messages = array_slice($this->messages, -self::MAX_MESSAGES);
        }

        session(['chatbot' => [
            'messages' => $this->messages,
            'open'     => $this->isOpen,
            'lang'     => $this->chatLang,
        ]]);
    }

    /**
     * Localised UI strings for the current chat language.
     * Kept self-contained (no __() keys) so the bot localises independently.
     */
    #[Computed]
    public function ui(): array
    {
        $strings = [
            'en' => [
                'title' => 'Win Win Assistant',
                'online' => 'Online now',
                'clear' => 'Reset',
                'close' => 'Close chat',
                'open' => 'Chat with us',
                'placeholder' => 'Type your message…',
                'send' => 'Send',
                'powered' => 'Auto-reply · WhatsApp us for live help',
                'quick' => 'Quick questions',
                'wa' => 'WhatsApp us',
                'lang' => 'Language',
            ],
            'ms' => [
                'title' => 'Pembantu Win Win',
                'online' => 'Dalam talian',
                'clear' => 'Set semula',
                'close' => 'Tutup sembang',
                'open' => 'Sembang dengan kami',
                'placeholder' => 'Taip mesej anda…',
                'send' => 'Hantar',
                'powered' => 'Balas automatik · WhatsApp kami untuk bantuan',
                'quick' => 'Soalan pantas',
                'wa' => 'WhatsApp kami',
                'lang' => 'Bahasa',
            ],
            'zh' => [
                'title' => 'Win Win 智能助手',
                'online' => '在线',
                'clear' => '重置',
                'close' => '关闭对话',
                'open' => '与我们聊天',
                'placeholder' => '输入您的消息…',
                'send' => '发送',
                'powered' => '自动回复 · 需要真人请 WhatsApp',
                'quick' => '快捷提问',
                'wa' => 'WhatsApp 联系',
                'lang' => '语言',
            ],
        ];

        return $strings[$this->chatLang] ?? $strings['en'];
    }

    /**
     * Suggested-question chips for the current language.
     * Each chip carries a lucide icon name, a label, and the query to send.
     */
    #[Computed]
    public function suggestions(): array
    {
        $chips = [
            'en' => [
                ['icon' => 'calendar', 'label' => 'Book appointment', 'query' => 'I want to book an appointment'],
                ['icon' => 'package', 'label' => 'Products', 'query' => 'What products do you sell?'],
                ['icon' => 'clock', 'label' => 'Opening hours', 'query' => 'What are your operating hours?'],
                ['icon' => 'map-pin', 'label' => 'Location', 'query' => 'Where are you located?'],
                ['icon' => 'volume-2', 'label' => 'Car audio', 'query' => 'Tell me about car audio systems'],
                ['icon' => 'banknote', 'label' => 'Pricing', 'query' => 'How much does it cost?'],
            ],
            'ms' => [
                ['icon' => 'calendar', 'label' => 'Buat tempahan', 'query' => 'Saya mahu buat tempahan'],
                ['icon' => 'package', 'label' => 'Produk', 'query' => 'Apakah produk yang anda jual?'],
                ['icon' => 'clock', 'label' => 'Waktu operasi', 'query' => 'Apakah waktu operasi anda?'],
                ['icon' => 'map-pin', 'label' => 'Lokasi', 'query' => 'Di mana lokasi anda?'],
                ['icon' => 'volume-2', 'label' => 'Audio kereta', 'query' => 'Ceritakan tentang sistem audio kereta'],
                ['icon' => 'banknote', 'label' => 'Harga', 'query' => 'Berapakah harganya?'],
            ],
            'zh' => [
                ['icon' => 'calendar', 'label' => '预约服务', 'query' => '我想预约'],
                ['icon' => 'package', 'label' => '产品', 'query' => '你们卖什么产品？'],
                ['icon' => 'clock', 'label' => '营业时间', 'query' => '你们的营业时间是几点？'],
                ['icon' => 'map-pin', 'label' => '地址', 'query' => '你们在哪里？'],
                ['icon' => 'volume-2', 'label' => '汽车音响', 'query' => '介绍一下汽车音响系统'],
                ['icon' => 'banknote', 'label' => '价格', 'query' => '价格多少？'],
            ],
        ];

        return $chips[$this->chatLang] ?? $chips['en'];
    }

    public function switchLang(string $lang): void
    {
        if (! in_array($lang, ['en', 'ms', 'zh'], true) || $lang === $this->chatLang) {
            return;
        }

        // Change only the chat's language — the site stays as-is. Livewire
        // re-renders the widget in the new language; the choice is persisted so
        // it survives navigation. A short note tells the user the switch took.
        $this->chatLang = $lang;
        $this->isOpen = true;
        $this->messages[] = ['role' => 'assistant', 'text' => $this->langSwitchedNote()];
        $this->persist();
        $this->dispatch('chatbot-scroll');
    }

    /**
     * Reply and render in the language the customer wrote in. CJK → Chinese
     * (incl. Traditional), clear Malay markers → BM, latin text → English;
     * ambiguous input (emoji, digits, "ok") keeps the current language.
     */
    private function detectLang(string $msg, string $current): string
    {
        $msg = mb_strtolower($msg);

        if (preg_match('/[\x{4e00}-\x{9fff}]/u', $msg)) {
            return 'zh';
        }

        if (preg_match('/\b(saya|awak|anda|boleh|berapa|macam|mana|tak|tiada|ada|nak|kedai|harga|bila|tempah|pasang|hantar|barang|dengan|untuk|kereta|baiki|ansuran|waranti|tukar|jam|buka|tutup|guna|jual|beli|murah|mahal)\b/iu', $msg)) {
            return 'ms';
        }

        if (preg_match('/[a-z]{2,}/i', $msg)) {
            return 'en';
        }

        return in_array($current, ['en', 'ms', 'zh'], true) ? $current : 'en';
    }

    private function langSwitchedNote(): string
    {
        return match ($this->chatLang) {
            'ms' => "Baik! Saya akan berbual dalam Bahasa Melayu sekarang. 😊 Apa yang boleh saya bantu?",
            'zh' => "好的！我现在用中文为您服务。😊 有什么可以帮您？",
            default => "Done! I'll chat in English now. 😊 How can I help?",
        };
    }

    private function pushGreeting(): void
    {
        $greetings = [
            'en' => "Hi there! 👋 I'm the Win Win Assistant.\n\nAsk me about products, bookings, pricing, opening hours, location — or tap a quick question below to get started.",
            'ms' => "Hai! 👋 Saya Pembantu Win Win.\n\nTanya saya tentang produk, tempahan, harga, waktu operasi atau lokasi — atau ketik soalan pantas di bawah untuk bermula.",
            'zh' => "你好！👋 我是 Win Win 智能助手。\n\n可以问我产品、预约、价格、营业时间或地址，也可以点击下方的快捷提问开始。",
        ];

        $this->messages[] = ['role' => 'assistant', 'text' => $greetings[$this->chatLang] ?? $greetings['en']];
    }

    public function open(): void
    {
        $this->isOpen = true;
        $this->persist();
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->persist();
    }

    public function clearChat(): void
    {
        $this->messages = [];
        $this->isLoading = false;
        $this->pendingText = null;
        $this->pushGreeting();
        $this->persist();
        $this->dispatch('chatbot-scroll');
    }

    /**
     * Send a predefined question from a suggestion chip.
     */
    public function quickAsk(string $query): void
    {
        $this->userInput = $query;
        $this->sendMessage();
    }

    public function sendMessage(): void
    {
        $text = trim($this->userInput);

        if ($text === '' || $this->isLoading) {
            return;
        }

        // Defence-in-depth: strip any HTML before further processing.
        $text = strip_tags($text);

        if (mb_strlen($text) > self::MAX_INPUT_LENGTH) {
            $text = mb_substr($text, 0, self::MAX_INPUT_LENGTH);
        }

        $ip = request()->ip();

        // ── Security gate: malicious payload / spam detection ──────────────
        if ($this->isUnderAbuseBlock($ip)) {
            $this->userInput = '';
            $this->messages[] = ['role' => 'assistant', 'text' => $this->safetyMessage()];
            $this->persist();
            $this->dispatch('chatbot-scroll');
            return;
        }

        if ($category = $this->classifyBlocked($text)) {
            $this->registerThreat($ip);
            $this->userInput = '';
            // Never echo blocked input back into the transcript.
            $this->messages[] = [
                'role' => 'assistant',
                'text' => $category === 'security' ? $this->safetyMessage() : $this->moderationMessage(),
            ];
            $this->persist();
            $this->dispatch('chatbot-scroll');
            return;
        }

        // ── Rate limiting (two tiers: burst per-minute + sustained per-hour) ─
        $throttleKey = 'chatbot:' . $ip;
        $hourlyKey = 'chatbot_hourly:' . $ip;

        if (RateLimiter::tooManyAttempts($throttleKey, self::RATE_LIMIT_MAX)
            || RateLimiter::tooManyAttempts($hourlyKey, self::HOURLY_MAX)) {
            $seconds = max(
                RateLimiter::availableIn($throttleKey),
                RateLimiter::tooManyAttempts($hourlyKey, self::HOURLY_MAX) ? RateLimiter::availableIn($hourlyKey) : 0
            );
            $phone = config('services.store.phone_display');
            $this->messages[] = ['role' => 'user', 'text' => $text];
            $this->messages[] = ['role' => 'assistant', 'text' => match ($this->chatLang) {
                'ms' => "Terlalu banyak mesej. Sila tunggu {$seconds} saat, atau WhatsApp kami di {$phone}.",
                'zh' => "发送消息过于频繁，请等待 {$seconds} 秒后再试，或直接 WhatsApp 我们：{$phone}。",
                default => "Too many messages. Please wait {$seconds} seconds, or WhatsApp us at {$phone}.",
            }];
            $this->userInput = '';
            $this->persist();
            $this->dispatch('chatbot-scroll');
            return;
        }

        RateLimiter::hit($throttleKey, self::RATE_LIMIT_DECAY);
        RateLimiter::hit($hourlyKey, 3600);

        // Step 1: show the user message + typing indicator immediately, then
        // process the AI reply in a follow-up request so the loading state is
        // actually rendered (a single request would set true→false invisibly).
        $this->messages[] = ['role' => 'user', 'text' => $text];
        $this->userInput = '';
        $this->pendingText = $text;
        $this->isLoading = true;
        $this->persist();
        $this->dispatch('chatbot-scroll');
        $this->dispatch('chatbot-focus');
        $this->dispatch('chatbot-generate');
    }

    /**
     * Step 2: generate the assistant reply for the pending user message.
     */
    #[On('chatbot-generate')]
    public function generateReply(): void
    {
        if (! $this->isLoading || $this->pendingText === null) {
            return;
        }

        $pending = $this->pendingText;
        $suggestions = [];

        // Switch the whole widget (replies + selector + placeholder + quick
        // questions) to the language the customer wrote in, without a page
        // refresh. Livewire re-renders with the new $chatLang.
        $detected = $this->detectLang($pending, $this->chatLang);
        if ($detected !== $this->chatLang) {
            $this->chatLang = $detected;
        }

        // "What page am I on?" — answered locally from the captured route.
        if ($this->isCurrentPageQuestion($pending)) {
            $reply = $this->currentPageReply();
        } else {
            $recent = array_slice($this->messages, -self::MAX_HISTORY);
            $aiMessages = collect($recent)
                ->filter(fn (array $m) => isset($m['text']))
                ->map(fn (array $message) => [
                    'role'    => $message['role'] === 'user' ? 'user' : 'assistant',
                    'content' => $message['text'],
                ])
                ->values()
                ->all();

            try {
                $response = $this->ai()->chat($aiMessages, 'lang:' . $this->chatLang);
                $reply = $response['message'];
                $suggestions = $response['suggestions'] ?? [];
            } catch (\Throwable) {
                $phone = config('services.store.phone_display');
                $reply = match ($this->chatLang) {
                    'ms' => "Masalah sambungan. Sila WhatsApp kami di {$phone} untuk bantuan segera.",
                    'zh' => "连接出现问题，请直接 WhatsApp 我们：{$phone}。",
                    default => "Connection issue. Please WhatsApp us at {$phone} for immediate assistance.",
                };
            }
        }

        $assistantMessage = ['role' => 'assistant', 'text' => $reply];

        // "Did you mean…" / follow-up chips rendered under the bot bubble.
        if ($suggestions !== []) {
            $assistantMessage['suggest'] = $suggestions;
        }

        // Attach a page-redirect call-to-action when a navigation intent is found.
        if ($cta = $this->detectNavigation($pending)) {
            $assistantMessage['cta'] = $cta;
        }

        $this->messages[] = $assistantMessage;
        $this->isLoading = false;
        $this->pendingText = null;
        $this->persist();
        $this->dispatch('chatbot-scroll');
        $this->dispatch('chatbot-focus');
    }

    /**
     * Detect a page-navigation intent and return a CTA button definition.
     */
    private function detectNavigation(string $text): ?array
    {
        $t = mb_strtolower($text);

        $has = fn (array $words) => collect($words)->contains(fn ($w) => str_contains($t, $w));

        // Order matters: more specific intents (tracking) are checked first.
        $intents = [
            [
                'words' => ['track order', 'order status', 'my order', 'where is my order', 'status pesanan', 'jejak pesanan', '订单状态', '查订单', '我的订单'],
                'route' => 'track-order',
                'label' => ['en' => 'Track your order', 'ms' => 'Jejak pesanan anda', 'zh' => '查询订单状态'],
            ],
            [
                'words' => ['track booking', 'my booking', 'booking status', 'check booking', 'status tempahan', 'jejak tempahan', '预约状态', '查预约', '我的预约'],
                'route' => 'booking.track',
                'label' => ['en' => 'Track your booking', 'ms' => 'Jejak tempahan anda', 'zh' => '查询预约状态'],
            ],
            [
                'words' => ['book', 'appointment', 'schedule', 'reserve', 'slot', 'tempah', 'temujanji', '预约', '预订', '约时间', '订位'],
                'route' => 'booking',
                'label' => ['en' => 'Open the Booking page', 'ms' => 'Buka halaman Tempahan', 'zh' => '前往预约页面'],
            ],
            [
                'words' => ['service', 'install', 'fitting', 'servis', 'pasang', 'pemasangan', '服务', '安装', '装'],
                'route' => 'services',
                'label' => ['en' => 'View our Services', 'ms' => 'Lihat Servis kami', 'zh' => '查看服务项目'],
            ],
            [
                'words' => ['gallery', 'showcase', 'portfolio', 'galeri', 'hasil kerja', '作品', '相册', '案例', '展示'],
                'route' => 'gallery',
                'label' => ['en' => 'Browse our Gallery', 'ms' => 'Lihat Galeri kami', 'zh' => '浏览作品相册'],
            ],
            [
                'words' => ['contact', 'whatsapp', 'call', 'reach', 'hubungi', 'telefon', '联系', '联络', '电话'],
                'route' => 'contact',
                'label' => ['en' => 'Go to Contact page', 'ms' => 'Pergi ke halaman Hubungi', 'zh' => '前往联系页面'],
            ],
            [
                'words' => ['product', 'accessory', 'accessories', 'buy', 'shop', 'catalog', 'produk', 'aksesori', 'beli', '产品', '配件', '购买', '商品'],
                'route' => 'products',
                'label' => ['en' => 'Browse Products', 'ms' => 'Lihat Produk', 'zh' => '浏览产品'],
            ],
        ];

        foreach ($intents as $intent) {
            if ($has($intent['words'])) {
                return [
                    'label' => $intent['label'][$this->chatLang] ?? $intent['label']['en'],
                    'url'   => route($intent['route']),
                ];
            }
        }

        return null;
    }

    /**
     * Detect a "what page am I on?" question.
     */
    private function isCurrentPageQuestion(string $text): bool
    {
        $patterns = [
            '/\b(what|which)\s+page\b/i',
            '/\bwhere am i\b/i',
            '/\bcurrent page\b/i',
            '/\bwhat\s+(is\s+)?this\s+page\b/i',
            '/halaman (apa|mana)/iu',
            '/saya (di|berada di|kat) (halaman|mana)/iu',
            '/(哪一?页|哪个页面|当前页面|现在.*页面|这是什么页|我在.*页)/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Friendly, localised name for the page the chatbot is embedded on.
     */
    private function currentPageName(): ?string
    {
        $names = [
            'home'              => ['en' => 'Home', 'ms' => 'Laman Utama', 'zh' => '首页'],
            'products'          => ['en' => 'Products', 'ms' => 'Produk', 'zh' => '产品'],
            'product.show'      => ['en' => 'Product Details', 'ms' => 'Butiran Produk', 'zh' => '产品详情'],
            'services'          => ['en' => 'Services', 'ms' => 'Servis', 'zh' => '服务'],
            'booking'           => ['en' => 'Booking', 'ms' => 'Tempahan', 'zh' => '预约'],
            'booking.track'     => ['en' => 'Booking Tracker', 'ms' => 'Jejak Tempahan', 'zh' => '预约查询'],
            'booking.manage'    => ['en' => 'Manage Booking', 'ms' => 'Urus Tempahan', 'zh' => '管理预约'],
            'gallery'           => ['en' => 'Gallery', 'ms' => 'Galeri', 'zh' => '作品集'],
            'track-order'       => ['en' => 'Order Tracking', 'ms' => 'Jejak Pesanan', 'zh' => '订单查询'],
            'about'             => ['en' => 'About Us', 'ms' => 'Tentang Kami', 'zh' => '关于我们'],
            'contact'           => ['en' => 'Contact', 'ms' => 'Hubungi Kami', 'zh' => '联系我们'],
            'faq'               => ['en' => 'FAQ', 'ms' => 'Soalan Lazim', 'zh' => '常见问题'],
            'privacy-policy'    => ['en' => 'Privacy Policy', 'ms' => 'Dasar Privasi', 'zh' => '隐私政策'],
            'terms-of-service'  => ['en' => 'Terms of Service', 'ms' => 'Terma Perkhidmatan', 'zh' => '服务条款'],
            'cart'              => ['en' => 'Cart', 'ms' => 'Troli', 'zh' => '购物车'],
            'checkout'          => ['en' => 'Checkout', 'ms' => 'Pembayaran', 'zh' => '结账'],
            'my-orders'         => ['en' => 'My Orders', 'ms' => 'Pesanan Saya', 'zh' => '我的订单'],
            'profile'           => ['en' => 'My Profile', 'ms' => 'Profil Saya', 'zh' => '个人资料'],
            'login'             => ['en' => 'Login', 'ms' => 'Log Masuk', 'zh' => '登录'],
        ];

        if (! isset($names[$this->currentRoute])) {
            return null;
        }

        return $names[$this->currentRoute][$this->chatLang] ?? $names[$this->currentRoute]['en'];
    }

    private function currentPageReply(): string
    {
        $page = $this->currentPageName();

        if ($page === null) {
            return match ($this->chatLang) {
                'ms' => "Anda sedang melayari laman web Win Win Car Studio. 🌐\n\nMahu saya bawa anda ke Produk, Servis, atau Tempahan? Tanya sahaja!",
                'zh' => "您正在浏览 Win Win Car Studio 的网站。🌐\n\n需要我带您去产品、服务或预约页面吗？随时告诉我！",
                default => "You're browsing the Win Win Car Studio website. 🌐\n\nWant me to take you to Products, Services, or Booking? Just ask!",
            };
        }

        return match ($this->chatLang) {
            'ms' => "Anda sedang berada di halaman「{$page}」. 📍\n\nMahu saya bawa anda ke tempat lain? Tanya sahaja!",
            'zh' => "您当前在「{$page}」页面。📍\n\n需要我带您去其他页面吗？随时告诉我！",
            default => "You're currently on the「{$page}」page. 📍\n\nWant me to take you somewhere else? Just ask!",
        };
    }

    /**
     * Classify a message as 'security' (injection/spam), 'inappropriate'
     * (sexual / hateful / violent / illegal content), or null if it is fine.
     */
    private function classifyBlocked(string $text): ?string
    {
        if ($this->containsThreat($text)) {
            return 'security';
        }

        if ($this->isInappropriate($text)) {
            return 'inappropriate';
        }

        return null;
    }

    /**
     * Detect script/SQL/PHP injection, code payloads, and spam/gambling links.
     */
    private function containsThreat(string $text): bool
    {
        $patterns = [
            '/<\s*script\b/i',
            '/<\s*\/?\s*(iframe|object|embed|svg|img|link|meta|style)\b/i',
            '/javascript\s*:/i',
            '/\bon[a-z]+\s*=/i',                       // inline event handlers
            '/<\?php|<\?=/i',
            '/\b(union\s+select|select\s+.+\s+from|insert\s+into|drop\s+table|truncate\s+table|update\s+.+\s+set|delete\s+from|alter\s+table)\b/i',
            '/([\'"])\s*(or|and)\s+[\'"]?\d+[\'"]?\s*=\s*[\'"]?\d+/i', // ' OR 1=1
            '/\b(base64_decode|eval|assert|system|exec|shell_exec|passthru|proc_open|phpinfo|fwrite|file_put_contents)\s*\(/i',
            '/\$\{.*\}/',                              // ${...} template injection
            '/\{\{.*\}\}/',                            // {{...}} template injection
            '/\.\.\/|\.\.\\\\/',                       // path traversal
            '/\b(judi\s*online|slot\s*gacor|togel|sportsbook|bandar\s*bola|situs\s*judi|casino\s*online|deposit\s*pulsa|viagra|cialis|porn)\b/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect sexual, hateful/racist, violent, or illegal-drug content.
     * Tokens are kept specific to avoid false positives in car-related chat
     * (e.g. "kill the engine", "shoot a video").
     */
    private function isInappropriate(string $text): bool
    {
        $patterns = [
            // Sexual / pornographic
            '/\b(porn|pornography|xxx|nsfw|hentai|blowjob|nude|naked|masturbat|escort\s*service|sex\s*(chat|video|cam)|lucah|bogel)\b/i',
            '/(色情|黄片|裸照|性爱|做爱|av女优|约炮)/u',
            // Hate speech / racism / slurs
            '/\b(nigger|chink|faggot|retard|white\s*power|kill\s*all\s*\w+|ethnic\s*cleansing|genocide|racial\s*slur)\b/i',
            '/\b(racism|racist|discriminat\w*)\b/i',
            '/(种族歧视|歧视|种族清洗)/u',
            // Violence / weapons how-to
            '/\b(make|build|create)\s+a?\s*(bomb|explosive|weapon)\b/i',
            '/\b(how\s+to\s+kill|kill\s+(someone|a\s*person)|kidnap|behead)\b/i',
            '/(制造炸弹|如何杀人|杀人方法|绑架)/u',
            // Illegal drugs
            '/\b(buy|sell|make)\s+(cocaine|heroin|meth|methamphetamine|drugs|dadah)\b/i',
            '/\b(cocaine|heroin|methamphetamine)\b/i',
            '/(毒品|冰毒|海洛因)/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    private function registerThreat(string $ip): void
    {
        $key = 'chatbot_threat:' . $ip;
        $count = (int) Cache::get($key, 0) + 1;
        Cache::put($key, $count, now()->addSeconds(self::ABUSE_BLOCK_SECONDS));

        if ($count >= self::ABUSE_BLOCK_THRESHOLD) {
            Cache::put('chatbot_block:' . $ip, true, now()->addSeconds(self::ABUSE_BLOCK_SECONDS));
        }
    }

    private function isUnderAbuseBlock(string $ip): bool
    {
        return Cache::has('chatbot_block:' . $ip);
    }

    private function safetyMessage(): string
    {
        $phone = config('services.store.phone_display');

        return match ($this->chatLang) {
            'ms' => "Maaf, saya hanya boleh membantu dengan pertanyaan tentang Win Win Car Studio (produk, servis, tempahan, dan lain-lain).\n\nUntuk bantuan lanjut, WhatsApp kami di {$phone}.",
            'zh' => "抱歉，我只能协助与 Win Win Car Studio 相关的问题（产品、服务、预约等）。\n\n如需进一步协助，请 WhatsApp 我们：{$phone}。",
            default => "Sorry, I can only help with questions about Win Win Car Studio (products, services, bookings, and so on).\n\nFor anything else, WhatsApp us at {$phone}.",
        };
    }

    private function moderationMessage(): string
    {
        return match ($this->chatLang) {
            'ms' => "Saya di sini untuk membantu hal Win Win Car Studio — produk, aksesori, servis, dan tempahan. Saya tidak boleh membantu dengan topik itu.\n\nAda apa-apa tentang kereta anda yang boleh saya bantu? 🚗",
            'zh' => "我在这里只协助与 Win Win Car Studio 相关的事务——产品、配件、服务和预约。这个话题我无法协助。\n\n有什么关于您爱车的问题我可以帮忙吗？🚗",
            default => "I'm here to help with Win Win Car Studio — products, accessories, services, and bookings. I can't help with that topic.\n\nIs there something about your car I can assist with? 🚗",
        };
    }

    public function render()
    {
        return view('livewire.ai-chatbot');
    }
}
