<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // SECURITY FIX (AUTH-VULN-02): the app previously relied on
        // Laravel's stock Password::defaults(), which only requires 8
        // characters and nothing else. This applies to every flow that uses
        // Password::defaults() (password-change in PasswordController,
        // password-reset in NewPasswordController) without touching
        // anything else about the login/session business process.
        Password::defaults(function () {
            $rule = Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols();

            return $this->app->isProduction() ? $rule->uncompromised() : $rule;
        });
    }
}
