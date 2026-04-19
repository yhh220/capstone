<?php

namespace App\Providers;

use App\Contracts\AiServiceInterface;
use App\Services\Ai\MockDriver;
use App\Services\Ai\OllamaDriver;
use Illuminate\Support\ServiceProvider;

class AiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AiServiceInterface::class, function () {
            return match (config('ai.driver')) {
                'ollama' => new OllamaDriver(),
                default => new MockDriver(),
            };
        });
    }
}
