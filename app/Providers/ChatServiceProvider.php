<?php

namespace App\Providers;

use App\Contracts\ChatServiceInterface;
use App\Services\Chat\MockDriver;
use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ChatServiceInterface::class, MockDriver::class);
    }
}
