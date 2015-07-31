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
use Wandu\Http\Factory\ServerRequestFactory;

// psr-7 server request request
$request = ServerRequestFactory::fromGlobals();

$router = new Router();

$router->get('/', function () {
    return "Hello World :D";
});

$contents = $router->dispatch($request);

echo $contents; // "Hello World :D"
```

## Documents

### Constructor



### Get

```php
```