<?php

namespace Laravel\Forge;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ForgeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(ForgeManager::class, function ($app) {
            return new ForgeManager($app['config']->get('services.forge.token'));
        });
    }
}
