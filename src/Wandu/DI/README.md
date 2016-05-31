Wandu DI
===

[![Latest Stable Version](https://poser.pugx.org/wandu/di/v/stable.svg)](https://packagist.org/packages/wandu/di)
[![Latest Unstable Version](https://poser.pugx.org/wandu/di/v/unstable.svg)](https://packagist.org/packages/wandu/di)
[![Total Downloads](https://poser.pugx.org/wandu/di/downloads.svg)](https://packagist.org/packages/wandu/di)
[![License](https://poser.pugx.org/wandu/di/license.svg)](https://packagist.org/packages/wandu/di)

Dependency Injection Container.

inspired by [pimple](http://pimple.sensiolabs.org) and [Laravel Container](http://laravel.com/docs/5.1/container)

## Installation

Use [Composer](http://getcomposer.org) to install Wandu DI into your project:

```bash
composer require wandu/di
```

## Documents

**Container Methods**

- Basic Methods
    1. [instance](#1-instance)
    1. [closure](#2-closure)
    1. [bind](#3-bind)
    1. [alias](#4-alias)

- Chaining Methods
    1. [freeze](#1-freeze)
    1. [wire](#2-wire)
    1. [factory](#3-factory)

- Service Provider
    1. [register](#1-register)
    1. [boot](#2-boot)
    
- Auto Resolving Methods
    1. [create](#1-create)
    1. [call](#2-call)

- Auto Wiring Methods
    1. [inject](#1-inject)

- Other Methods
    1. [get](#1-get)
    1. [has](#2-has)
    1. [freeze](#3-freeze)
    1. [destroy](#4-destroy)
    1. [extend](#5-extend)


### Simple Example

```php
$container = new \Wandu\DI\Container();

$container->instance('config', [
    'username' => 'db_username',
    'password' => 'db_password',
]);
$container->bind(DbAdapterInterface::class, MySqlAdapter::class);
$container->closure(Client::class, function (ContainerInterface $app) {
    return new Client(
        $app[DbAdapterInterface::class],
        $app['config']['username'],
        $app['config']['password']
    );
});
$container->alias('client', Client::class);

$container['client']; // return Client Object
$container[Client::class]; // return Client Object

// $container['client'] === $container[Client::class]
$this->assertSame(
    $container['client'],
    $container[Client::class]
);
```

## Document

### Basic Methods

Wandu-DI는 5가지 메서드를 이용하여 Container에 등록할 수 있습니다.

The Wandu-DI provides 4 type of default methods.

1. [instance](#1-instance)
1. [closure](#2-closure)
1. [bind](#3-bind)
1. [alias](#4-alias)

#### 1. Instance

**컨테이너**에 값을 집어넣을 때 사용합니다. `instance`메서드는 `offsetSet`에 연결되어 있어서 배열처럼 사용할 수 있습니다.

Use this method when you inject a object to the **Container**. This `instance` method is equal with `offsetSet`, so you can use this like array.

**Example**

```php
$container = new \Wandu\DI\Container();

$container->instance('config1', [
    'username' => 'db_username',
    'password' => 'db_password',
]);

$this->assertEquals([
    'username' => 'db_username',
    'password' => 'db_password',
], $container['config1']);

// offsetSet
$container['config2'] = [
    'username' => 'db_username',
    'password' => 'db_password',
];

$this->assertEquals([
    'username' => 'db_username',
    'password' => 'db_password',
], $container['config2']);

```

#### 2. Closure

`closure`는 클로저를 통해 호출 시점에 해당 객체를 생성할 수 있습니다.

This `closure` method create the object when call this object of name. 

**Example**

```php
$container = new \Wandu\DI\Container();

$container->closure(DbAdapterInterface::class, function () {
    return new MySqlAdapter();
});

$this->assertInstanceOf(
    DbAdapterInterface::class,
    $container[DbAdapterInterface::class]
);

```

#### 3. Bind

Auto Resolve를 위한 메서드입니다. 생성자 전체가 타입힌트가 지정되어 있는 객체일 때 사용가능합니다. 이 메서드를 호출할 때, 매개변수로는 기준이 되는 Interface와 실제로 구현된 Class 한쌍 가집니다. 만약 인터페이스 없이 클래스가 단독 형태로 존재한다면 Interface는 생략가능합니다.

**Example**

```php
$container = new \Wandu\DI\Container();

$container->bind(DbAdapterInterface::class, MySqlAdapter::class);

$this->assertInstanceOf(
    DbAdapterInterface::class,
    $container[DbAdapterInterface::class]
);
$this->assertInstanceOf(
    MySqlAdapter::class,
    $container[MySqlAdapter::class]
);

// also same
$this->assertSame($container[DbAdapterInterface::class], $container[MySqlAdapter::class]);

```

#### 4. Alias

별칭을 지정합니다. 즉, 지정된 이름을 통해 해당 컨테이너값에 접근할 때 사용할 수 있습니다.

**Example**

```php
$container = new \Wandu\DI\Container();

$container->bind(DbAdapterInterface::class, MySqlAdapter::class);
$container->alias('adapter', DbAdapterInterface::class);

// get by alias name
$this->assertInstanceOf(
    DbAdapterInterface::class,
    $container['adapter']
);

```

### Chaining Methods

1. [freeze](#1-freeze)
1. [wire](#2-wire)
1. [factory](#3-factory)

#### 1. Freeze

**Example**

#### 2. Wire

**Example**

#### 4. Factory

**Example**

### Service Provider

1. [register](#1-register)
1. [boot](#2-boot)

#### 1. Register

#### 2. Boot

### Auto Resolving Methods

Wandu-DI는 Auto Resolving을 지원합니다. 이를 위한 두가지의 매서드가 있습니다.

1. [create](#1-create)
1. [call](#2-call)

`create`와 `call`은 Container 내부에 어떠한 값도 변화를 주지 않습니다. 컨테이너 내부에 등록된 값을 기반으로 객체를 생성(`create`)하거나, 메서드를 호출(`call`)하도록 도와줍니다.

#### 1. Create

- `create(string $class, mixed[] $parameters)`

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

#### 2. Call

- `call(callable $callee, mixed[] $parameters)`

첫번째 매개변수 `$callee`는 callable한 6가지 모두([참고자료](http://blog.wani.kr/dev/php/php-something-4-callable/))를 지원합니다.

위의 `create` 메서드가 생성자를 호출한다면, `call`메서드는 모든 `callable`한 녀석을 컨테이너를 통해서 호출합니다.

### Auto Wiring Methods

1. `inject`

#### 1. Inject

> inject(object $object, array $parameters = [])

If you want to read more details,

 - [Inject Test Stub Files](tests/Stub/Inject)
 - [InjectTest.php](tests/InjectTest.php)
 
**Example**

**Direct inject.**

```php
class DirectInjectExample
{
    /** @var mixed */
    private $something;

    /** @var mixed */
    private $otherthing;

    /**
     * @return mixed
     */
    public function getSomething()
    {
        return $this->something;
    }
}
```

```php
public function testDirectInject()
{
    // inject object
    $example1 = new DirectInjectExample();
    $this->assertNull($example1->getSomething()); // null

    $something = new RequiredLibrary();
    $this->container->inject($example1, [
        'something' => $something
    ]);

    $this->assertSame($something, $example1->getSomething()); // same

    // inject anything
    $example2 = new DirectInjectExample();
    $this->assertNull($example2->getSomething());

    $this->container->inject($example2, [
        'something' => 12341234
    ]);

    $this->assertSame(12341234, $example2->getSomething()); // same
}
```

**Auto Inject**

```php
<?php
class AutoInjectExample
{
    /**
     * @Autowired
     * @var WanduDIStubRequiredLibraryInterface
     */
    private $requiredLibrary;

    /**
     * @return WanduDIStubRequiredLibraryInterface
     */
    public function getRequiredLibrary()
    {
        return $this->requiredLibrary;
    }
}
```

```php
public function testAutoInjectWithSuccess()
{
    $this->container->bind(RequiredLibraryInterface::class, RequiredLibrary::class);

    $example = new AutoInjectExample();
    $this->assertNull($example->getRequiredLibrary());

    $this->container->inject($example);

    // inject success!
    $this->assertInstanceOf(RequiredLibraryInterface::class, $example->getRequiredLibrary());
}
```

### Other Methods

1. [get](#1-get)
1. [has](#2-has)
1. [freeze](#3-freeze)
1. [destroy](#4-destroy)
1. [extend](#5-extend)

#### 1. Get

**Example**

#### 2. Has

**Example**

#### 3. Freeze

**Example**

#### 4. Destroy

**Example**

#### 5. Extend

**Example**
