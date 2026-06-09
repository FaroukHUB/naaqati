<?php

namespace App\Providers;

use App\Support\CurrentRelais;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Contexte du relais courant, partagé sur toute la requête (multi-relais).
        $this->app->singleton(CurrentRelais::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
