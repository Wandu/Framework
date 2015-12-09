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

Based on [Jicjjang/June](https://github.com/jicjjang/June). :D

## Basic Usage

```php
<?php
use Wandu\Router\ClassLoader\DefaultLoader;
use Wandu\Router\Dispatcher;
use Wandu\Router\Exception\HandlerNotFoundException;
use Wandu\Router\Exception\MethodNotAllowedException;
use Wandu\Router\Exception\RouteNotFoundException;
use Wandu\Router\Router;

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

