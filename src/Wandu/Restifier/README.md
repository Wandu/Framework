Wandu Restifier
=================

[![Latest Stable Version](https://poser.pugx.org/wandu/restifier/v/stable.svg)](https://packagist.org/packages/wandu/restifier)
[![Latest Unstable Version](https://poser.pugx.org/wandu/restifier/v/unstable.svg)](https://packagist.org/packages/wandu/restifier)
[![Total Downloads](https://poser.pugx.org/wandu/restifier/downloads.svg)](https://packagist.org/packages/wandu/restifier)
[![License](https://poser.pugx.org/wandu/restifier/license.svg)](https://packagist.org/packages/wandu/restifier)

Transform Data to the RESTFul API Output.

## Installation

`composer require wandu/restifier`

## Documentation

**Example**

```php
$restifier = new Restifier();
$restifier->addTransformer(SampleUser::class, new SampleUserTransformer());
$restifier->addTransformer(SampleCustomer::class, new SampleCustomerTransformer());

$user = new SampleUser([
    'username' => 'wan2land',
    'customer' => new SampleCustomer([
        'address' => 'seoul blabla',
        'paymentmethods' => [], // critical data
    ]),
]);

static::assertEquals([
    "username" => "wan2land",
    'customer' => [
        'address' => 'seoul blabla',
    ],
], $restifier->restify($user));
```

**Restifier**

```php
class Restifier {
    public function addTransformer(string $classNameOrInterfaceName, callable $transformer);
    
    public function restify($resource, array $includes = [], callable $transformer = null): array|null
    
    public function restifyMany($resource, array $includes = [], callable $transformer = null): array
} 
```

### Transformer

Transformer is callable. It is recommended to use the callable class that contain `__invoke` method.

**Example**

```php
<?php
namespace Wandu\Restifier\Sample;

use Wandu\Restifier\Contracts\Restifiable;

class SampleUserTransformer
{
    public function __invoke(SampleUser $user, Restifiable $restifier, array $includes = [])
    {
        return [
            'username' => $user->username,
            'customer' => $restifier->restify($user->customer),
        ];
    }

    public function customer(SampleUser $user, Restifiable $restifier, array $includes = [])
    {
        return [
            'customer' => $restifier->restify($user->customer, $includes),
        ];
    }

    public function profile(SampleUser $user, Restifiable $restifier, array $includes = [])
    {
        return [
            'profile' => $user->profile,
        ];
    }
}
```
