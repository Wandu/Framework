Wandu Router
===

[![Latest Stable Version](https://poser.pugx.org/wandu/router/v/stable.svg)](https://packagist.org/packages/wandu/router)
[![Latest Unstable Version](https://poser.pugx.org/wandu/router/v/unstable.svg)](https://packagist.org/packages/wandu/router)
[![Total Downloads](https://poser.pugx.org/wandu/router/downloads.svg)](https://packagist.org/packages/wandu/router)
[![License](https://poser.pugx.org/wandu/router/license.svg)](https://packagist.org/packages/wandu/router)

[![Build Status](https://img.shields.io/travis/Wandu/Router/master.svg)](https://travis-ci.org/Wandu/Router)
[![Code Coverage](https://scrutinizer-ci.com/g/Wandu/Router/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Wandu/Router/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Wandu/Router/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Wandu/Router/?branch=master)

FastRouter with PSR-7 Wrapper Library.

## Basic Usage

```php
<?php
use Wandu\Router\ClassLoader\DefaultLoader;
use Wandu\Router\Dispatcher;
use Wandu\Router\Exception\HandlerNotFoundException;
use Wandu\Router\Exception\MethodNotAllowedException;
use Wandu\Router\Exception\RouteNotFoundException;
use Wandu\Router\Router;

// first of argument is class loader. there are 3 predefined loader.
// 1. DefaultLoader
// 2. ArrayAccessLoader
// 3. WanduLoader : with wandu/di package.
$dispatcher = new Dispatcher(new DefaultLoader(), [
    'virtual_method_enabled' => false,
    'cache_enabled' => true,
    'cache_file' => __DIR__ . '/routes.cache.php',
]);

$dispatcher = $dispatcher->withRouter(function (Router $router) {
    $router->prefix('/admin', function (Router $router) {
        $router->get('/pages', PageController::class, 'index');
        $router->get('/users', UserController::class, 'index');
        $router->get('/users/{user}', UserController::class, 'show');
    });
});

try {
    $response = $dispatcher->dispatch($request); // $request is PSR7 ServerRequestInterface
} catch (MethodNotAllowedException $e) {
    // reutrn 405
} catch (RouteNotFoundException $e) {
    // return 404
} catch (HandlerNotFoundException $e) {
    // return 500
}
```

## Middleware

**Middleware**

```php
<?php
namespace Wandu\Router\Stubs;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\MiddlewareInterface;

class CookieMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return mixed
     */
    public function handle(ServerRequestInterface $request, callable $next)
    {
        $request = $request->withAttribute('cookie', ['name' => 'wan2land']);
        return $next($request);
    }
}
```

**Router With Middlewares**

```php
$dispatcher = $dispatcher->withRouter(function (Router $router) {
    $router->middleware([CookieMiddleware::class], function (Router $router) {
        ...
    });
});
```
