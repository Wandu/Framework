Wandu Router
===

[![Latest Stable Version](https://poser.pugx.org/wandu/router/v/stable.svg)](https://packagist.org/packages/wandu/router)
[![Latest Unstable Version](https://poser.pugx.org/wandu/router/v/unstable.svg)](https://packagist.org/packages/wandu/router)
[![Total Downloads](https://poser.pugx.org/wandu/router/downloads.svg)](https://packagist.org/packages/wandu/router)
[![License](https://poser.pugx.org/wandu/router/license.svg)](https://packagist.org/packages/wandu/router)

FastRoute with PSR-7 Wrapper Library.

## Installation

```bash
composer require wandu/router
```

## Basic Usage

```php
$dispatcher = new \Wandu\Router\Dispatcher();
$routes = $dispatcher->createRouteCollection();

$routes->get('/', HomeController::class);
$routes->get('/users', UserController::class, 'index');
$routes->get('/users/:id', UserController::class, 'show');

$request = new ServerRequest('GET', '/'); // PSR7 ServerRequestInterface implementation
$response = $dispatcher->dispatch($routes, $request);

static::assertInstanceOf(ResponseInterface::class, $response);
static::assertEquals('index', $response->getBody()->__toString());

$request = new ServerRequest('GET', '/nothing'); // PSR7 ServerRequestInterface implementation
try {
    $dispatcher->dispatch($routes, $request);
} catch (RouteNotFoundException $e) {
    static::assertEquals('Route not found.', $e->getMessage());
}
```

```php
class HomeController
{
    public static function index()
    {
        return new Response(200, new StringStream("index"));
    }
}
```

## Pattern Routes

```php
$routes->get('/users/:id(\d+)?', UserController::class, 'show');
$routes->get('/users-:id', UserController::class, 'show');
```

```php
class UserController
{
    public static function show(ServerRequestInterface $request)
    {
        return new Response(200, new StringStream("{$request->getAttribute('id')}"));
    }
}
```

You can use all patterns in [path-to-regexp](https://github.com/pillarjs/path-to-regexp).

## Reference

 - [nikic/FastRoute](https://github.com/nikic/FastRoute).
