<?php

namespace App\Services\Ai;

use App\Contracts\AiServiceInterface;
use App\Models\AiLog;
use App\Models\Product;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OllamaDriver implements AiServiceInterface
{
    public function chat(array $messages, ?string $systemPrompt = null): string
    {
        $response = $this->callChat('chat', $messages, $systemPrompt);

        return $response['message'] ?? 'Sorry, I could not process that request right now.';
    }

    public function recommend(string $query, Collection $products): array
    {
        $catalog = $products->map(fn (Product $product) => [
            'id' => $product->id,
            'name' => $product->name,
            'brand' => $product->brand,
            'category' => $product->category?->name,
            'price' => $product->current_price,
            'short_description' => $product->short_description,
        ])->values()->all();

        $response = $this->callChat(
            'recommend',
            [[
                'role' => 'user',
                'content' => "Customer query:\n{$query}\n\nProduct catalog:\n" . json_encode($catalog, JSON_UNESCAPED_UNICODE),
            ]],
            <<<PROMPT
You are a product assistant for Win Win Car Audio Auto Accessories in Shah Alam, Malaysia.
Only recommend products from the provided catalog.
Respond in the same language as the customer.
Keep it concise and return JSON only:
{"recommendations":[{"product_id":1,"reason":"..."}],"follow_up":"..."}
PROMPT
        );

        return $this->parseJsonPayload($response['message'] ?? '', [
            'recommendations' => [],
            'follow_up' => 'Please WhatsApp us at 016-915 0917 for more help.',
        ]);
    }

    public function generateDescription(Product $product): array
    {
        $response = $this->callChat(
            'generate_description',
            [[
                'role' => 'user',
                'content' => json_encode([
                    'name' => $product->name,
                    'brand' => $product->brand,
                    'description' => $product->description,
                    'short_description' => $product->short_description,
                    'specs' => $product->specs,
                    'compatible_vehicles' => $product->compatible_vehicles,
                ], JSON_UNESCAPED_UNICODE),
            ]],
            'You are a marketing copywriter for a Malaysian auto accessories shop. Return JSON only: {"en":"...","ms":"...","zh":"..."} with concise professional descriptions.'
        );

        return $this->parseJsonPayload($response['message'] ?? '', [
            'en' => '',
            'ms' => '',
            'zh' => '',
        ]);
    }

    protected function callChat(string $feature, array $messages, ?string $systemPrompt = null): array
    {
        $requestPayload = [
            'model' => config('ai.ollama.model'),
            'stream' => false,
            'messages' => $this->buildMessages($messages, $systemPrompt),
        ];

        try {
            $response = $this->client()
                ->post(rtrim(config('ai.ollama.host'), '/') . '/api/chat', $requestPayload)
                ->throw()
                ->json();

            $message = data_get($response, 'message.content');

            if (!is_string($message) || trim($message) === '') {
                throw new RuntimeException('Ollama returned an empty response.');
            }

            AiLog::record([
                'driver' => 'ollama',
                'feature' => $feature,
                'request_payload' => $requestPayload,
                'response_payload' => $response,
                'status' => 'success',
                'ip_address' => request()->ip(),
            ]);

            return ['message' => $message, 'raw' => $response];
        } catch (\Throwable $exception) {
            AiLog::record([
                'driver' => 'ollama',
                'feature' => $feature,
                'request_payload' => $requestPayload,
                'response_payload' => null,
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'ip_address' => request()->ip(),
            ]);

            throw $exception;
        }
    }

    protected function client(): PendingRequest
    {
        return Http::acceptJson()->timeout(config('ai.ollama.timeout', 20));
    }

    protected function buildMessages(array $messages, ?string $systemPrompt): array
    {
        $compiled = [];

        if ($systemPrompt) {
            $compiled[] = [
                'role' => 'system',
                'content' => $systemPrompt,
            ];
        }

        foreach ($messages as $message) {
            $compiled[] = [
                'role' => $message['role'] ?? 'user',
                'content' => $message['content'] ?? '',
            ];
        }

        return $compiled;
    }

    protected function parseJsonPayload(string $payload, array $fallback): array
    {
        $decoded = json_decode($payload, true);

        if (is_array($decoded)) {
            return array_replace($fallback, $decoded);
        }

        if (preg_match('/\{.*\}/s', $payload, $matches) === 1) {
            $decoded = json_decode($matches[0], true);

            if (is_array($decoded)) {
                return array_replace($fallback, $decoded);
            }
        }

        return $fallback;
    }
}
