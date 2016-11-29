<?php
namespace Wandu\Collection\Contracts;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Serializable;

/**
 * @todo diff, diffKeys, intersect, isEmpty, merge, union
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
