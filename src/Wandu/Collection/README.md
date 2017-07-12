Wandu Collection
===

[![Latest Stable Version](https://poser.pugx.org/wandu/collection/v/stable.svg)](https://packagist.org/packages/wandu/collection)
[![Latest Unstable Version](https://poser.pugx.org/wandu/collection/v/unstable.svg)](https://packagist.org/packages/wandu/collection)
[![Total Downloads](https://poser.pugx.org/wandu/collection/downloads.svg)](https://packagist.org/packages/wandu/collection)
[![License](https://poser.pugx.org/wandu/collection/license.svg)](https://packagist.org/packages/wandu/collection)

Collection Library Like Java. Provides List, Map, and Set.

## Installation

```bash
composer require wandu/collection
```

## Usage

### List

there is one list.

- `Wandu\Collection\ArrayList`

**Interface**

```php
<?php
namespace Wandu\Collection\Contracts;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Serializable;

interface ListInterface extends ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Serializable
{
    /**
     * @return string
     */
    public function __toString();

    /**
     * @return array
     */
    public function toArray();
    
    /**
     * @return void
     */
    public function clear();

    /**
     * @param array ...$values
     * @return boolean
     */
    public function contains(...$values);

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);

    /**
     * @param string[] ...$keys
     */
    public function remove(...$keys);

    /**
     * @param string[] ...$keys
     */
    public function has(...$keys);

    /**
     * @param callable $handler
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function filter(callable $handler = null);

    /**
     * @param callable $handler
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function map(callable $handler);

    /**
     * @param callable $handler
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $handler, $initial = null);

    /**
     * @param \Wandu\Collection\Contracts\ListInterface $list
     * @return \Wandu\Collection\Contracts\MapInterface
     */
    public function combine(ListInterface $list);

    /**
     * @param callable $handler
     * @return \Wandu\Collection\Contracts\MapInterface<\Wandu\Collection\Contracts\ListInterface>
     */
    public function groupBy(callable $handler);

    /**
     * @param callable $handler
     * @return \Wandu\Collection\Contracts\MapInterface
     */
    public function keyBy(callable $handler);

    /**
     * @return array
     */
    public function all();
    
    /**
     * @param callable $handler
     * @param mixed $default
     * @return mixed
     */
    public function first(callable $handler = null, $default = null);

    /**
     * @param callable $handler
     * @param mixed $default
     * @return mixed
     */
    public function last(callable $handler = null, $default = null);

    /**
     * @param \Wandu\Collection\Contracts\ListInterface $list
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function intersect(ListInterface $list);

    /**
     * @param \Wandu\Collection\Contracts\ListInterface $list
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function union(ListInterface $list);

    /**
     * @param \Wandu\Collection\Contracts\ListInterface $list
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function merge(ListInterface $list);

    /**
     * @param string $glue
     * @return string
     */
    public function implode($glue = null);

    /**
     * @return boolean
     */
    public function isEmpty();

    /**
     * @return mixed
     */
    public function pop();

    /**
     * @param mixed[] ...$values
     * @return $this
     */
    public function push(...$values);

    /**
     * @return mixed
     */
    public function shift();

    /**
     * @param mixed[] ...$values
     * @return $this
     */
    public function unshift(...$values);

    /**
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function reverse();

    /**
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function shuffle();

    /**
     * @param callable $callback
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function sort(callable $callback = null);

    /**
     * @param int $offset
     * @param int $length
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function slice($offset, $length = null);

    /**
     * @param int $offset
     * @param int $length
     * @param mixed $replacement
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function splice($offset, $length = null, $replacement = null);

    /**
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function unique();
}
```

### Map

there is one map.

- `Wandu\Collection\ArrayMap`

**Interface**

```php
<?php
namespace Wandu\Collection\Contracts;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Serializable;

/**
 * @todo diff, diffKeys, intersect, merge, union,
 */
interface MapInterface extends ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Serializable
{
    /**
     * @return string
     */
    public function __toString();

    /**
     * @return array
     */
    public function all();
    
    /**
     * @return array
     */
    public function toArray();
    
    /**
     * @return void
     */
    public function clear();

    /**
     * @return boolean
     */
    public function isEmpty();
    
    /**
     * @param array ...$values
     * @return boolean
     */
    public function contains(...$values);

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);

    /**
     * @param string[] ...$keys
     */
    public function remove(...$keys);

    /**
     * @param string[] ...$keys
     */
    public function has(...$keys);

    /**
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function keys();

    /**
     * @return \Wandu\Collection\Contracts\ListInterface
     */
    public function values();

    /**
     * @param callable $handler
     * @return \Wandu\Collection\Contracts\MapInterface
     */
    public function map(callable $handler);

    /**
     * @param callable $handler
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $handler, $initial = null);
}
```
