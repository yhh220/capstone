<?php

namespace App\Livewire;

use App\Contracts\AiServiceInterface;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class AiChatbot extends Component
{
    public bool $isOpen = false;
    public string $userInput = '';
    public array $messages = [];
    public bool $isLoading = false;
    public string $chatLang = '';

    private const MAX_INPUT_LENGTH = 500;
    private const MAX_HISTORY = 20;
    private const RATE_LIMIT_MAX = 10;
    private const RATE_LIMIT_DECAY = 60;

    private function ai(): AiServiceInterface
    {
        return app(AiServiceInterface::class);
    }

    public function selectLang(string $lang): void
    {
        $allowed = ['en', 'ms', 'zh'];
        if (!in_array($lang, $allowed, true)) {
            return;
        }

        $this->chatLang = $lang;

        $greetings = [
            'en' => "Hi there! 👋 Welcome to Win Win Car Studio.\n\nI can help you with:\n• Products & accessories\n• Workshop bookings\n• Operating hours & location\n• Installation & pricing\n• Warranty info\n\nWhat can I help you with today?",
            'ms' => "Hai! 👋 Selamat datang ke Win Win Car Studio.\n\nSaya boleh membantu anda dengan:\n• Produk & aksesori\n• Tempahan bengkel\n• Waktu operasi & lokasi\n• Pemasangan & harga\n• Maklumat waranti\n\nApa yang boleh saya bantu hari ini?",
            'zh' => "你好！👋 欢迎光临 Win Win Car Studio。\n\n我可以帮您解答：\n• 产品与配件\n• 工坊预约\n• 营业时间与地址\n• 安装与价格\n• 保修资讯\n\n请问有什么可以帮您？",
        ];

        $this->messages[] = ['role' => 'assistant', 'text' => $greetings[$lang]];
    }

    public function open(): void
    {
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function clearChat(): void
    {
        $this->messages = [];
        $this->chatLang = '';
    }

    public function sendMessage(): void
    {
        $text = trim($this->userInput);

        if ($text === '' || $this->isLoading || $this->chatLang === '') {
            return;
        }

        if (mb_strlen($text) > self::MAX_INPUT_LENGTH) {
            $text = mb_substr($text, 0, self::MAX_INPUT_LENGTH);
        }

        $throttleKey = 'chatbot:' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::RATE_LIMIT_MAX)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $phone = config('services.store.phone_display');
            $this->messages[] = ['role' => 'user', 'text' => $text];
            $this->messages[] = ['role' => 'assistant', 'text' => match ($this->chatLang) {
                'ms' => "Terlalu banyak mesej. Sila tunggu {$seconds} saat, atau WhatsApp kami di {$phone}.",
                'zh' => "发送消息过于频繁，请等待 {$seconds} 秒后再试，或直接 WhatsApp 我们：{$phone}。",
                default => "Too many messages. Please wait {$seconds} seconds, or WhatsApp us at {$phone}.",
            }];
            $this->userInput = '';
            return;
        }

        RateLimiter::hit($throttleKey, self::RATE_LIMIT_DECAY);

        $this->messages[] = ['role' => 'user', 'text' => $text];
        $this->userInput = '';
        $this->isLoading = true;

        $recent = array_slice($this->messages, -self::MAX_HISTORY);
        $aiMessages = collect($recent)
            ->map(fn (array $message) => [
                'role'    => $message['role'] === 'user' ? 'user' : 'assistant',
                'content' => $message['text'],
            ])
            ->values()
            ->all();

        try {
            $reply = $this->ai()->chat($aiMessages, 'lang:' . $this->chatLang);
        } catch (\Throwable) {
            $phone = config('services.store.phone_display');
            $reply = match ($this->chatLang) {
                'ms' => "Masalah sambungan. Sila WhatsApp kami di {$phone} untuk bantuan segera.",
                'zh' => "连接出现问题，请直接 WhatsApp 我们：{$phone}。",
                default => "Connection issue. Please WhatsApp us at {$phone} for immediate assistance.",
            };
        }

        $this->messages[] = ['role' => 'assistant', 'text' => $reply];
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.ai-chatbot');
    }
}
