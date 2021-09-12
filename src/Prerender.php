<?php

namespace RenokiCo\LaravelPrerender;

use Closure;
use Illuminate\Http\Request;
use RenokiCo\Clusteer\Clusteer;

class Prerender
{
    /**
     * The callback to check wether the
     * request can be prerendered.
     *
     * @var \Closure|null
     */
    protected static $shouldPrerenderCallback;

    /**
     * The callback to modify the prerender call
     * before hitting the deck in Cluster.
     *
     * @var \Closure|null
     */
    protected static $mutateClusteerCallback;

    /**
     * Define the logic to check if
     * the request can be prerendered.
     *
     * @param  \Closure  $callback
     * @return bool
     */
    public static function shouldPrerender(Closure $callback)
    {
        static::$shouldPrerenderCallback = $callback;
    }

    /**
     * Check wether the request should be prerendered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function shouldBePrerendered(Request $request): bool
    {
        $callback = static::$shouldPrerenderCallback;

        return $callback($request);
    }

    /**
     * Define the logic to mutate the prerender request.
     *
     * @param  \Closure  $callback
     * @return bool
     */
    public static function mutateClusteerOnRequest(Closure $callback)
    {
        static::$mutateClusteerCallback = $callback;
    }

    /**
     * Check wether the request can be prerendered.
     *
     * @param  \RenokiCo\Clusteer\Clusteer  $prerender
     * @param  \Illuminate\Http\Request  $request
     * @return \RenokiCo\Clusteer\Clusteer
     */
    public static function mutateClusteer(Clusteer $prerender, Request $request)
    {
        $callback = static::$mutateClusteerCallback;

        return $callback
            ? $callback($prerender, $request)
            : $prerender;
    }
}
