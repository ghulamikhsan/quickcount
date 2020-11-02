<?php

namespace Energibangsa\cepet;

use Illuminate\Support\ServiceProvider;

class CepetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Energibangsa\Cepet\controllers\BaseController');
        $this->loadViewsFrom(__DIR__.'/views', 'views');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }
}
