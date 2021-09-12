<?php

namespace RenokiCo\LaravelPrerender\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use RenokiCo\Clusteer\Clusteer;
use RenokiCo\LaravelPrerender\Prerender;

class ShouldPrerender
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Prerender::shouldBePrerendered($request)) {
            return $this->prerenderRequest($request, $next($request));
        }

        return $next($request);
    }

    /**
     * Handle the prerendering request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    public function prerenderRequest(Request $request, Response $response)
    {
        try {
            $clusteer = Prerender::mutateClusteer(
                Clusteer::to($request->fullUrl())
                    ->waitUntilAllRequestsFinish()
                    ->setUserAgent('Clusteerbot/2.0')
                    ->withHtml(),
                $request,
            )->get();
        } catch (Exception $e) {
            return $response;
        }

        $response->setContent($clusteer->getHtml());

        return $response;
    }
}
