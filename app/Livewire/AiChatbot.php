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

    private const MAX_INPUT_LENGTH = 500;
    private const MAX_HISTORY = 20;
    private const RATE_LIMIT_MAX = 10;
    private const RATE_LIMIT_DECAY = 60;

    private function ai(): AiServiceInterface
    {
        return app(AiServiceInterface::class);
    }

    private function systemPrompt(): string
    {
        $name = config('services.store.name');
        $address = config('services.store.address');
        $phone = config('services.store.phone_display');
        $fb = config('services.store.facebook_url');
        $hours = config('services.store.hours')
            ?: 'Monday-Thursday 10:30-20:00 | Friday: CLOSED | Saturday 10:30-20:00 | Sunday 10:30-18:00';

        return <<<PROMPT
You are the AI assistant for {$name}, a car accessories shop in Shah Alam, Malaysia.

Business details:
- Services: car accessories sales, installation, modification, car audio
- Operating hours: {$hours}
- Location: {$address}
- WhatsApp: {$phone}
- Facebook: {$fb}

Instructions:
- Answer questions about products, services, bookings, pricing, and installation.
- Be friendly, concise, and professional.
- For complex enquiries or pricing details, recommend the customer WhatsApp us at {$phone}.
- Remind customers Friday is our rest day.
- Respond in the same language the customer uses.
- Do not make up specific prices.
PROMPT;
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
    }

    public function sendMessage(): void
    {
        $text = trim($this->userInput);

        if ($text === '' || $this->isLoading) {
            return;
        }

        if (mb_strlen($text) > self::MAX_INPUT_LENGTH) {
            $text = mb_substr($text, 0, self::MAX_INPUT_LENGTH);
        }

        $throttleKey = 'chatbot:' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::RATE_LIMIT_MAX)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->messages[] = ['role' => 'user', 'text' => $text];
            $this->messages[] = [
                'role' => 'assistant',
                'text' => "Too many messages. Please wait {$seconds} seconds before sending again, or WhatsApp us at +60169150917.",
            ];
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
                'role' => $message['role'] === 'user' ? 'user' : 'assistant',
                'content' => $message['text'],
            ])
            ->values()
            ->all();

        try {
            $reply = $this->ai()->chat($aiMessages, $this->systemPrompt());
        } catch (\Throwable) {
            $reply = 'Connection issue. Please WhatsApp us at +60169150917 for immediate assistance.';
        }

        $this->messages[] = ['role' => 'assistant', 'text' => $reply];
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.ai-chatbot');
    }
}
