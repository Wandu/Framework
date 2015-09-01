Wandu DI
===

[![Latest Stable Version](https://poser.pugx.org/wandu/di/v/stable.svg)](https://packagist.org/packages/wandu/di)
[![Latest Unstable Version](https://poser.pugx.org/wandu/di/v/unstable.svg)](https://packagist.org/packages/wandu/di)
[![Total Downloads](https://poser.pugx.org/wandu/di/downloads.svg)](https://packagist.org/packages/wandu/di)
[![License](https://poser.pugx.org/wandu/di/license.svg)](https://packagist.org/packages/wandu/di)

[![Build Status](https://travis-ci.org/Wandu/DI.svg?branch=master)](https://travis-ci.org/Wandu/DI)
[![Code Coverage](https://scrutinizer-ci.com/g/Wandu/DI/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Wandu/DI/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Wandu/DI/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Wandu/DI/?branch=master)

Dependency Injection Container.

inspired by [pimple](http://pimple.sensiolabs.org) and [Laravel Container](http://laravel.com/docs/5.1/container)

## Installation

### via Composer

`composer require wandu/di`

## Hot to use

### Simple Example

```php
<?php
namespace Your\Own\Space;

use ArrayAccess;
use Wandu\DI\Container;
use Wandu\DI\ContainerInterface;
use Your\Own\Client;
use Your\Own\RequirePackage;
use Your\Own\RequirePackageInterface;

$container = new Container();

$container->bind(RequirePackageInterface::class, RequirePackage::class);
$container->closure(Client::class, function (ContainerInterface $app, ArrayAccess $config) {
    return new Client($app[RequirePackageInterface::class], $config['client']);
});
$container->alias('client', Client::class);

$container['client']; // return Manager
$container[Client::class]; // return Manager also;

$container['client'] === $container[Client::class]; // true

```

## Document

### Methods

Wandu-DI는 5가지 메서드를 이용하여 Container에 등록할 수 있습니다.

The Wandu-DI provides 4 type of default methods.

1. `closure` (same `singleton` of 1.x)
2. `instance`
3. `bind`
4. `wire` **new** on v2.2
5. `alias`

### 1. Closure

Closure는 클로저를 통해 Container에 내용을 등록할 수 있도록 도와줍니다.

#### Example.

```php
<?php
$container->closure('product', function (ContainerInterface $app, ArrayAccess $config) {
    return new Product();
});

$container['product'] instanceof Product; // true
```

### 2. Instance

Closure와 비슷하지만, Closure없이 직접 값을 바인딩 할 수 있습니다. `instance` 메서드는 `offsetSet`에 연결되어있습니다.

It is similar to the Closure. But, it doesn't need the Closure.

#### Example.

```php
<?php
$container->instance('product', new Product);
// $container['product'] = new Product; // right also. `offsetSet` alias to `instance`.

$container['product'] instanceof Product; // true
```

### 3. Bind

2.x에서 추가된 Bind입니다. Auto Resolve를 위한 메서드입니다. 생성자 전체가 타입힌트가 지정되어 있는 객체일 때 사용가능
합니다. 기준이 되는 Interface와 실제로 구현된 Class 한쌍을 매개변수로 사용하지만, Interface는 생략가능합니다.

#### Example.

```php
class Product
{
    public function __construct(RequireInterface $require)
    {
        /* ... */
    }
}

$container->bind(RequireInterface::class, RequirePackage::class);
// $container->bind(RequirePackage::class); // right also.
$container->bind(Product::class);

$container[Product::class] instanceof Product; // true
```
### 4. Wire

2.2d에서 추가된 메서드입니다. 동작은 Bind와 거의 유사합니다. Bind와 동일하게 객체를 생성합니다. 그리고 객체내에
`@Autowired`가 마킹된 Property가 있을 경우 해당 객체에 해당하는 값을 주입해줍니다.

#### Example.

```php
class Product
{
    /**
     * @Autowired 전체 경로로만 사용가능합니다.
     * @var \Your\OwnNamespace\RequireInterface
     */
    private $property;

    public function getProperty()
    {
        return $this->property;
    }
}

$container->bind(RequireInterface::class, RequirePackage::class);

$container->wire(Product::class);

$container[Product::class] instanceof Product; // true
$container[Product::class]->getProperty() instanceof RequireInterface; // true
```


### 5. Alias

다른 이름을 통해 해당 Container값에 접근할 때 사용합니다.

#### Example.

```php
<?php
// alias with closure
$container->closure('product', function (ContainerInterface $app) {
    return new Product();
});
$container->alias('alias-product', 'product');

$container['product'] === $container['alias-product']; // true
```

## Auto Resolving

Wandu-DI는 Auto Resolving을 지원합니다. 이를 위한 두가지의 매서드가 있습니다.

1. `create`
2. `call`
3. `get` (내부적으로 `create`를 통해 호출합니다.)

3번은 `create`를 통해 호출하기 때문에 따로 설명하지 않겠습니다. `create`와 `call`은 Container 내부에 어떠한 값도
변화를 주지 않습니다. 컨테이너 내부에 등록된 값을 기반으로 객체를 생성(`create`)하거나, 메서드를 호출(`call`)하도록
도와줍니다.

### Create

- `create(string $class, mixed[] ...$parameters)`

아주 단순하게 다음과 같이 사용이 가능합니다.

```php
class Client
{
    public function __construct(Package $package)
    {
        /* do something */
    }
}

$container->create(Client::class);
```

물론 매개변수에는 모두 type-hint를 사용중이어야 합니다. 그리고 해당 매개변수는 모두 `$container`내부에 포함하고 있어야
합니다.

만약에 type-hint를 포함하고 있지 않다면 다음과 같이 사용가능합니다.

```php
class Client
{
    public function __construct(Package $package, $other, $theOther)
    {
        /* do something */
    }

    // 다음과 같이 사용해도 결과는 같습니다.
    // public function __construct($other, Package $package, $theOther)
    // public function __construct($other, $theOther, Package $package)

}

$container->create(Client::class, "other string", "the other string"); // return Client object
```

직접 매개변수를 지정해주어야 합니다. 이때 type-hint가 없는 매개변수의 위치는 상관없습니다. 순서만 지켜서 사용하시면
됩니다. 만약에 type-hint가 없는 매개변수의 숫자보다 전달할 값의 갯수가 적으면 `CannotResolveException`이 발생합니다.

### Call

- `call(callable $callee, mixed[] ...$parameters)`

첫번째 매개변수 `$callee`는 callable한 6가지 모두([참고자료](http://blog.wani.kr/dev/php/php-something-4-callable/))를
지원합니다.

위의 `create` 메서드가 생성자를 호출한다면, `call`메서드는 모든 `callable`한 녀석을 컨테이너를 통해서 호출합니다.

## Auto-Wired

..작성중..

## Other Methods

### Extend

#### Example.

```php
<?php
$container->closure('product', function (ContainerInterface $app) {
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

use ArrayAccess;
use Wandu\DI\ContainerInterface;
use Wandu\DI\Container;
use Wandu\DI\ServiceProviderInterface;

class YourOwnProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $app, ArrayAccess $config)
    {
        $app->closure('product', function (ContainerInterface $app, ArrayAccess $config) {
            return new Product($app['required-product']);
        });
    }
}

class YourRequiredPackageProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $app, ArrayAccess $config)
    {
        $app->closure('required-product', function (ContainerInterface $app, ArrayAccess $config) {
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
