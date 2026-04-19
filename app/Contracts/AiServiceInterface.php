<?php

namespace App\Contracts;

use App\Models\Product;
use Illuminate\Support\Collection;

interface AiServiceInterface
{
    public function chat(array $messages, ?string $systemPrompt = null): string;

    public function recommend(string $query, Collection $products): array;

    public function generateDescription(Product $product): array;
}
