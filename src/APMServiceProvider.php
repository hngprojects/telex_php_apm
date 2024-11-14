<?php

namespace TelexAPM;

use Illuminate\Support\ServiceProvider;
use TelexAPM\Core\APMCore;
use TelexAPM\Middleware\APMMiddleware;

class APMServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/telex.php', 'apm'
        );

        $this->app->singleton('apm', function ($app) {
            return new APMCore();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/telex.php' => config_path('telex.php'),
        ], 'config');

        $this->app['router']->aliasMiddleware('apm', APMMiddleware::class);

        // Load middleware globally if configured
        if (config('apm.enabled')) {
            $this->app['router']->pushMiddlewareToGroup('web', APMMiddleware::class);
        }
    }
}