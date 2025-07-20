<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;

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
    public function boot(UrlGenerator $url): void
    {
        // Force HTTPS in production
        if (app()->environment('production')) {
            $url->forceScheme('https');
        }

        // Override password reset URL structure
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return config('app.frontend_url') . "/reset-password?token={$token}&email={$user->email}";
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            $url = config('app.url') . "/api/auth/email/verify";
            $id = $notifiable->getKey();
            $hash = sha1($notifiable->getEmailForVerification());

            return "{$url}/{$id}/{$hash}";
        });
    }
}
