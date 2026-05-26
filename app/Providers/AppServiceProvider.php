<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
{
    if (env('APP_ENV') === 'production') {
        \URL::forceScheme('https');
    }

    $this->app->bind(\App\Services\BatchPaymentService::class, function ($app) {
        return new \App\Services\BatchPaymentService(
            $app->make(\App\Services\CircleService::class),
            $app->make(\App\Services\PolicyService::class),
        );
    });
}
}