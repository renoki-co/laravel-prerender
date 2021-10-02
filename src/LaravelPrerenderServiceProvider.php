<?php

namespace RenokiCo\LaravelPrerender;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Jenssegers\Agent\Facades\Agent;

class LaravelPrerenderServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        Request::macro('isFromClusteer', function () {
            /** @var \Illuminate\Http\Request $this */
            return $this->userAgent() === 'Clusteerbot/3.0';
        });

        $this->registerPrerenderChecks();
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

    /**
     * Register the prerender default checks.
     *
     * @return void
     */
    protected function registerPrerenderChecks(): void
    {
        Prerender::shouldPrerender(function (Request $request) {
            // Avoid infinite loop by excluding Clusteerbot/3.0 from prerendering,
            // because Clusteer is mimicking the browser.
            if ($request->isFromClusteerbot()) {
                return false;
            }

            if (! is_null($request->query('_escaped_fragment_'))) {
                return true;
            }

            if (Agent::isRobot() || $request->hasHeader('X-BUFFERBOT')) {
                return true;
            }

            return false;
        });
    }
}
