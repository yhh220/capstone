<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class AiChatbot extends Component
{
    public bool   $isOpen    = false;
    public string $userInput = '';
    public array  $messages  = [];
    public bool   $isLoading = false;

    private string $systemPrompt = <<<'PROMPT'
You are the AI assistant for Win Win Car Studio Auto Accessories, a car accessories shop in Shah Alam, Malaysia.

Business details:
- Name: Win Win Car Studio Auto Accessories
- Services: car accessories sales, installation, modification, car audio
- Operating hours: Monday–Thursday 10:30–20:00 | Friday: CLOSED | Saturday 10:30–20:00 | Sunday 10:30–18:00
- Location: No. 22, Ground Floor, Jalan Dinar C U3/C, Taman Subang Perdana, Seksyen U3, Shah Alam, 40150, Malaysia
- WhatsApp: +60169150917
- Facebook: https://www.facebook.com/winwincaraudio/

Instructions:
- Answer questions about products, services, bookings, pricing, and installation
- Be friendly, concise, and professional
- For complex enquiries or pricing details, recommend the customer WhatsApp us at +60169150917
- Remind customers Friday is our rest day
- Respond in the same language the customer uses (English, Bahasa Malaysia, or Chinese)
- Do NOT make up specific prices — say "please contact us on WhatsApp for the latest pricing"
PROMPT;

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

        $this->messages[] = ['role' => 'user', 'text' => $text];
        $this->userInput  = '';
        $this->isLoading  = true;

        $apiKey = config('services.anthropic.key');

        if (!$apiKey) {
            $this->messages[] = ['role' => 'assistant', 'text' => 'AI assistant is not configured yet. Please contact us on WhatsApp at +60169150917.'];
            $this->isLoading  = false;
            return;
        }

        // Build messages in Anthropic format
        $apiMessages = collect($this->messages)
            ->map(fn ($m) => ['role' => $m['role'] === 'user' ? 'user' : 'assistant', 'content' => $m['text']])
            ->values()
            ->toArray();

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(15)->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 512,
                'system'     => $this->systemPrompt,
                'messages'   => $apiMessages,
            ]);

            $reply = $response->json('content.0.text')
                ?? 'Sorry, I could not process your message. Please try again or WhatsApp us at +60169150917.';
        } catch (\Throwable) {
            $reply = 'Connection issue. Please WhatsApp us at +60169150917 for immediate assistance.';
        }

        $this->messages[] = ['role' => 'assistant', 'text' => $reply];
        $this->isLoading  = false;
    }

    public function render()
    {
        return view('livewire.ai-chatbot');
    }
}
