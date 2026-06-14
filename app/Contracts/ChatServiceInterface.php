<?php

namespace App\Contracts;

use App\Models\Product;
use Illuminate\Support\Collection;

interface ChatServiceInterface
{
    /**
     * Answer a chat conversation.
     *
     * @return array{message: string, suggestions?: array<int, array{label: string, query: string}>}
     */
    public function chat(array $messages, ?string $systemPrompt = null): array;

    public function recommend(string $query, Collection $products): array;

    public function generateDescription(Product $product): array;
}
