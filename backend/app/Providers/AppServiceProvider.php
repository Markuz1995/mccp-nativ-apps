<?php

namespace App\Providers;

use App\Contracts\AiClientInterface;
use App\Integrations\Gemini\GeminiClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AiClientInterface::class, fn () => new GeminiClient());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
