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

@code("../../../tests/Restifier/RestifierTest.php@testTransformer")

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

@code("../../../tests/Restifier/Sample/SampleUserTransformer.php")
