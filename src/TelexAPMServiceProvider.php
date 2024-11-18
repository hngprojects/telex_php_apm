<?php

namespace TelexOrg\TelexAPM;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use TelexOrg\TelexAPM\Middleware\TelexAPMMiddleware;
use TelexOrg\TelexAPM\Exception\ExceptionHandler;

class TelexAPMServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/apm.php', 'myapm');
    }

    /**
     * Bootstrap any application services.
    */
    public function boot()
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/apm.php' => Config::get('config/apm.php'),
        ], 'config');

        // Register middleware
        $this->app['router']->pushMiddlewareToGroup('web', TelexAPMMiddleware::class);

        // Use custom exception handler
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            ExceptionHandler::class
        );
    }
}
