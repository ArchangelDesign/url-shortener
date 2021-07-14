<?php

namespace App\Providers;

use App\Services\ShortenerService;
use Illuminate\Support\ServiceProvider;

class ShortenerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ShortenerService::class, function ($app) {
            return new ShortenerService();
        });
    }
}
