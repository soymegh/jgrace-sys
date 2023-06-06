<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return "http://localhost:8080/reset-password/?token={$token}&email={$notifiable->getEmailForPasswordReset()}";
//            return "http://css.capital.software/reset-password/?token={$token}&email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
