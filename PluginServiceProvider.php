<?php

namespace Plugins\MoneyPlugin;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
class PluginServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Example: Blade directive
        Blade::directive('money-plugin', fn() => "<?php echo 'money plugin from plugin'; ?>");

        // Example: views
        $this->loadViewsFrom(__DIR__ . '/views', 'moneyPlugin');
        Route::middleware('web')->group(function () {
            require __DIR__ . '/routes.php';
        });

        }

    public function register()
    {
        //
    }
}