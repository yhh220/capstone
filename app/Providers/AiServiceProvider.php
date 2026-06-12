<?php

namespace App\Providers;

use App\Contracts\AiServiceInterface;
use App\Services\Ai\MockDriver;
use Illuminate\Support\ServiceProvider;

class AiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AiServiceInterface::class, MockDriver::class);
    }
}
