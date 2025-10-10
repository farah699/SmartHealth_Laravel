<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // <- ajouter cette ligne
use App\Services\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NotificationService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Limite la longueur par défaut des colonnes VARCHAR indexées à 191
        Schema::defaultStringLength(191);
    }

    protected $policies = [
    Activity::class => ActivityPolicy::class,
];
}
