<?php

namespace App\Services\Ai;

use App\Contracts\AiServiceInterface;
use App\Models\AiLog;
use App\Models\Product;
use Illuminate\Support\Collection;

class MockDriver implements AiServiceInterface
{
    public function chat(array $messages, ?string $systemPrompt = null): string
    {
        $lastMessage = collect($messages)->last()['content'] ?? '';

        $reply = 'AI is currently in demo mode. Please WhatsApp us at 016-915 0917 for detailed recommendations and latest pricing.';

        if (str_contains(mb_strtolower($lastMessage), 'booking')) {
            $reply = 'For bookings, please choose your service and preferred time on our booking page, or WhatsApp us at 016-915 0917 if you need help.';
        }

        AiLog::record([
            'driver' => 'mock',
            'feature' => 'chat',
            'request_payload' => ['messages' => $messages, 'system_prompt' => $systemPrompt],
            'response_payload' => ['message' => $reply],
            'status' => 'success',
            'ip_address' => request()->ip(),
        ]);

        return $reply;
    }

    public function recommend(string $query, Collection $products): array
    {
        $recommendations = $products
            ->take(3)
            ->map(fn (Product $product) => [
                'product_id' => $product->id,
                'reason' => 'Demo recommendation based on your query.',
            ])
            ->values()
            ->all();

        $response = [
            'recommendations' => $recommendations,
            'follow_up' => 'If you want a confirmed fitment check, WhatsApp us at 016-915 0917.',
        ];

        AiLog::record([
            'driver' => 'mock',
            'feature' => 'recommend',
            'request_payload' => ['query' => $query],
            'response_payload' => $response,
            'status' => 'success',
            'ip_address' => request()->ip(),
        ]);

        return $response;
    }

    public function generateDescription(Product $product): array
    {
        $response = [
            'en' => "{$product->name} is a reliable showroom-highlight accessory designed to improve compatibility, quality, and value for everyday drivers.",
            'ms' => "{$product->name} ialah aksesori pilihan yang menekankan keserasian, kualiti, dan nilai untuk pemandu harian.",
            'zh' => "{$product->name} 是一款强调兼容性、品质与性价比的精选汽车配件，适合日常驾驶使用。",
        ];

        AiLog::record([
            'driver' => 'mock',
            'feature' => 'generate_description',
            'request_payload' => ['product_id' => $product->id],
            'response_payload' => $response,
            'status' => 'success',
            'ip_address' => request()->ip(),
        ]);

        return $response;
    }
}
