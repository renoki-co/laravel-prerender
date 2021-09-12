Laravel Clusteer Prerender
==========================

![CI](https://github.com/renoki-co/laravel-prerender/workflows/CI/badge.svg?branch=master)
[![codecov](https://codecov.io/gh/renoki-co/laravel-prerender/branch/master/graph/badge.svg)](https://codecov.io/gh/renoki-co/laravel-prerender/branch/master)
[![StyleCI](https://github.styleci.io/repos/405476853/shield?branch=master)](https://github.styleci.io/repos/405476853)
[![Latest Stable Version](https://poser.pugx.org/renoki-co/laravel-prerender/v/stable)](https://packagist.org/packages/renoki-co/laravel-prerender)
[![Total Downloads](https://poser.pugx.org/renoki-co/laravel-prerender/downloads)](https://packagist.org/packages/renoki-co/laravel-prerender)
[![Monthly Downloads](https://poser.pugx.org/renoki-co/laravel-prerender/d/monthly)](https://packagist.org/packages/renoki-co/laravel-prerender)
[![License](https://poser.pugx.org/renoki-co/laravel-prerender/license)](https://packagist.org/packages/renoki-co/laravel-prerender)

Prerender Laravel pages using Clusteer and this nice package.

## 🤝 Supporting

If you are using one or more Renoki Co. open-source packages in your production apps, in presentation demos, hobby projects, school projects or so, spread some kind words about our work or sponsor our work via Patreon. 📦

You will sometimes get exclusive content on tips about Laravel, AWS or Kubernetes on Patreon and some early-access to projects or packages.

[<img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" height="41" width="175" />](https://www.patreon.com/bePatron?u=10965171)

## 🚀 Installation

You can install the package via composer:

```bash
composer require renoki-co/laravel-prerender
```

This package leverages [Clusteer](https://github.com/renoki-co/clusteer), a Puppeteer wrapper written for PHP [which is way faster than traditional packages](https://clusteer.renoki.org/getting-started/benchmarks) by running a long-lived process that can execute Puppeteer commands by opening new pages instead of opening new browsers for each website.

**Clusteer is already installed when you will be installing Laravel Prerender, all you need to do is to [read the installation guide for Clusteer](https://clusteer.renoki.org/getting-started/installation)**

After you have configured Clusteer, you may start the server with:

```bash
php artisan clusteer:serve
```

## 🙌 Usage

Laravel Prerender comes with a middleware you can attach to your web routes.

```php
use App\Http\Controller;
use RenokiCo\LaravelPrerender\Middleware\ShouldPrerender;

class Dashboard extends Controller
{
    public function __construct()
    {
        $this->middleware([
            ShouldPrerender::class,
        ]);
    }

    public function index()
    {
        return view('welcome');
    }
}
```

## Prerendering Technique

The default prerendering instructions are the following:

```php
use Illuminate\Http\Request;
use Jenssegers\Agent\Facades\Agent;
use RenokiCo\LaravelPrerender\Prerender;

Prerender::shouldPrerender(function (Request $request) {
    // Avoid infinite loop by excluding Clusteerbot/2.0 from prerendering,
    // because Clusteer is mimicking the browser.
    if ($request->userAgent() === 'Clusteerbot/2.0') {
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
```

You may overwrite the prerender technique in your `AppServiceProvider`'s `boot()` method. **Make sure to also avoid prerendering in case the `User-Agent` header is `Clusteerbot/2.0`**. In the future, this header might change according to the package updates, but they will be marked as breaking changes.

```php
use Illuminate\Http\Request;
use RenokiCo\LaravelPrerender\Prerender;

Prerender::shouldPrerender(function (Request $request) {
    // Avoid infinite loop by excluding Clusteerbot/2.0 from prerendering,
    // because Clusteer is mimicking the browser.
    if ($request->userAgent() === 'Clusteerbot/2.0') {
        return false;
    }

    return $request->isGoogleBot();
});
```

## Mutating the Clusteer request

The prerendering will be made using Clusteer's built-in PHP functions to get the rendered HTML. In some cases, you might want to modify the Clusteer's state with additional configs.

The `$clusteer` object is already initialized like below. You are free to append your own methods via the `mutateClusteerOnRequest` method in your `AppServiceProvider`'s `boot` method:

```php
Clusteer::to($request->fullUrl())
    ->waitUntilAllRequestsFinish()
    ->setUserAgent('Clusteerbot/2.0')
    ->withHtml();
```

```php
use RenokiCo\LaravelPrerender\Prerender;

Prerender::mutateClusteerOnRequest(function (Clusteer $clusteer, Request $request) {
    return $clusteer->blockExtensions(['.css']);
});
```

## 🐛 Testing

``` bash
vendor/bin/phpunit
```

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔒  Security

If you discover any security related issues, please email alex@renoki.org instead of using the issue tracker.

## 🎉 Credits

- [Alex Renoki](https://github.com/rennokki)
- [All Contributors](../../contributors)
