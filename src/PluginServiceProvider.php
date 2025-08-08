<?php

namespace Plugins\MoneyPlugin\src;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
class PluginServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Check for enabled.flag file
        if (!file_exists(__DIR__ . '/../enabled.flag')) {
            return; // ❌ Plugin is disabled
        }
        // Example: views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'MoneyPlugin');
        Route::middleware('web')->group(function () {
            require __DIR__ . '/../routes/routes.php';
        });

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

    }

    public function register()
    {
        //
    }
}