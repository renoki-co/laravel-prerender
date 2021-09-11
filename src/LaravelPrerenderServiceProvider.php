<?php

namespace RenokiCo\LaravelPrerender;

use Illuminate\Support\ServiceProvider;

class LaravelPrerenderServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/prerender.php' => config_path('prerender.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../config/prerender.php', 'prerender'
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
