<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EmailService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the EmailService as a singleton
        $this->app->singleton(EmailService::class, function ($app) {
            return new EmailService();
        });

        // You can also register other services here if needed
        $this->app->singleton('email-service', function ($app) {
            return $app->make(EmailService::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}