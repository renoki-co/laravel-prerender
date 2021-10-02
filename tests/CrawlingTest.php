<?php

namespace RenokiCo\LaravelPrerender\Test;

use Illuminate\Http\Request;
use RenokiCo\Clusteer\Clusteer;
use RenokiCo\LaravelPrerender\Prerender;

class CrawlingTest extends TestCase
{
    public function test_prerender_with_header_and_without_header()
    {
        Prerender::shouldPrerender(function (Request $request) {
            return ! $request->isFromClusteerBot();
        });

        Prerender::mutateClusteerOnRequest(function (Clusteer $clusteer, Request $request) {
            // Temporary mock the URL to the testing Node.js app.
            return $clusteer->setUrl('http://localhost:8000')->wait(2);
        });

        $this->withHeaders(['User-Agent' => 'Clusteerbot/3.0'])
            ->get(route('todos'))
            ->assertSee('Todo: {{ title }}');

        $this->withHeaders(['User-Agent' => 'Googlebot'])
            ->get(route('todos'))
            ->assertSee('Todo: delectus aut autem')
            ->assertDontSee('Todo: {{ title }}');
    }

    public function test_prerender_with_escaped_string()
    {
        Prerender::shouldPrerender(function (Request $request) {
            return ! $request->isFromClusteerBot();
        });

        Prerender::mutateClusteerOnRequest(function (Clusteer $clusteer, Request $request) {
            // Temporary mock the URL to the testing Node.js app.
            return $clusteer->setUrl('http://localhost:8000?_escaped_fragment_=')->wait(2);
        });

        $this->withHeaders(['User-Agent' => 'Clusteerbot/3.0'])
            ->get(route('todos'))
            ->assertSee('Todo: {{ title }}');

        $this->withHeaders(['User-Agent' => 'Googlebot'])
            ->get(route('todos'))
            ->assertSee('Todo: delectus aut autem')
            ->assertDontSee('Todo: {{ title }}');
    }
}
