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

