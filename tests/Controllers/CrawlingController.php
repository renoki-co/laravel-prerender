<?php

namespace RenokiCo\LaravelPrerender\Test\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use RenokiCo\LaravelPrerender\Middleware\ShouldPrerender;

class CrawlingController extends Controller
{
    /**
     * Initialize the controller.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware([
            ShouldPrerender::class,
        ]);
    }

    /**
     * Render the todos webpage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function todos(Request $request)
    {
        return file_get_contents(__DIR__.'/../fixtures/tester.html');
    }
}
