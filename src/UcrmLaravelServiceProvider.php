<?php

namespace jrbarna\UcrmLaravel;

use jrbarna\UcrmLaravel\Ucrm;
use Illuminate\Support\ServiceProvider;

class UcrmLaravelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('ucrm-laravel', function($app) {
            return new Ucrm();
        });

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'ucrm-laravel');

    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('ucrm-laravel.php'),
            ], 'config');

        }
    }
}
