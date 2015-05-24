Wandu Router
===

FastRouter with PSR-7 Wrapper Library.

based on [Jicjjang/June](https://github.com/jicjjang/June). :D

## Basic Usage

```php
use Wandu\Router\Router;

$request = new Your\Own\Psr7\Request; // this must be implemented Psr\Http\Message\ServerRequestInterface.
// recommend to use Wandu\Http
// $request = Wandu\Http\Factory\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

$router = new Router();
$router->get('/', function () {
    return "Hello World :D";
});

$contents = $router->dispatch($request);

echo $contents; // "Hello World :D"
```

