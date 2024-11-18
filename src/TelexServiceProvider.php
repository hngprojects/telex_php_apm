<?php

namespace TelexOrg\TelexAPM;

use Illuminate\Support\ServiceProvider;

class TelexServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/telex_config.php' => config_path('telex_config.php'),
        ], 'config');

        $this->app->singleton('laravel_telex_apm', function () {
            return new TelexHandler();
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/telex_config.php', 'telex_config'
        );
    }
}
