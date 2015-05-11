Wandu DI
===

[![Latest Stable Version](https://poser.pugx.org/wandu/di/v/stable.svg)](https://packagist.org/packages/wandu/di)
[![Latest Unstable Version](https://poser.pugx.org/wandu/di/v/unstable.svg)](https://packagist.org/packages/wandu/di)
[![Total Downloads](https://poser.pugx.org/wandu/di/downloads.svg)](https://packagist.org/packages/wandu/di)
[![License](https://poser.pugx.org/wandu/di/license.svg)](https://packagist.org/packages/wandu/di)
[![Build Status](https://img.shields.io/travis/Wandu/DI/master.svg)](https://travis-ci.org/Wandu/DI)

Dependency Injection Container.

inspired by [pimple](http://pimple.sensiolabs.org)

## Installation

### via Composer

`composer require wandu/di`

## Hot to use

### Simple Example

```php
<?php
namespace Your\Own\Space;

use Wandu\DI\Container;
use Your\Own\Plugin\Manager;
use Your\Own\Plugin\RequiredPackage;

$container = new Container();

$container->singleton(RequiredPackage::class, function (Container $app) {
    return new RequiredPackage;
});
$container->singleton(Manager::class, function (Container $app) {
    return new Manager($app[RequiredPackage::class]);
});
$container->alias('manager', Manager::class);


$container['manager']; // return Manager
$container[Manager::class]; // return Manager also;

$container['manager'] === $container[Manager::class]; // true

```

## Document

The Wandu-DI provides 4 type of default methods.

1. `singleton`
2. `factory`
3. `instance`
4. `alias`

### Singleton

#### Example.

```php
<?php
$container->singleton('product', function (Container $app) {
    return new Product();
});

$container['product'] == $container['product']; // true
$container['product'] === $container['product']; // true
```

### Factory

#### Example.

```php
<?php
$container->factory('product', function (Container $app) {
    return new Product();
});

$container['product'] == $container['product']; // true
$container['product'] === $container['product']; // false
```

### Instance

It is similar to the Singleton. But, it doesn't need the Closure.

#### Example.

```php
<?php
$container->instance('product', new Product);

$container['product'] == $container['product']; // true
$container['product'] === $container['product']; // true
```


### Alias

#### Example.

```php
<?php
// alias with singleton
$container->singleton('product', function (Container $app) {
    return new Product();
});
$container->alias('alias-product', 'product');

$container['product'] == $container['alias-product']; // true
$container['product'] === $container['alias-product']; // true
```

```php
<?php
// alias with factory
$container->factory('product', function (Container $app) {
    return new Product();
});
$container->alias('alias-product', 'product');

$container['product'] == $container['alias-product']; // true
$container['product'] === $container['alias-product']; // false
```

## Other Methods

### Extend

#### Example.

```php
<?php
$container->singleton('product', function (Container $app) {
    return new Product();
});
$container->extend('product', function (Product $item) {
    $item->contents = 'extended contents!';
    return $item;
});

echo $container['product']->contents; // 'extended contents!'
```

## Service Providers

#### Example.

```php
<?php
namespace Your\Own\Space;

use Wandu\DI\Container;
use Wandu\DI\ServiceProviderInterface;

class YourOwnProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app->singleton('product', function (Container $app) {
            return new Product($app['required-product']);
        });
    }
}

class YourRequiredPackageProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app->singleton('required-product', function (Container $app) {
            return new RequiredProduct();
        });
    }
}
```

```php
$container->register(new YourOwnProvider);
$container->register(new YourRequiredPackageProvider);

$container['product']; // will be return instance of Product type.
```
