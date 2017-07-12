<?php
namespace Wandu\Collection;

use InvalidArgumentException;
use Wandu\Collection\Contracts\MapInterface;

class ArrayMap implements MapInterface
{
    /** @var array */
    protected $items;

    /**
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $string = static::class . " [\n";
        foreach ($this->items as $key => $item) {
            $string .= "    \"{$key}\" => ";
            if (is_string($item)) {
                $string .= "\"{$item}\",\n";
            } elseif (is_scalar($item)) {
                $string .= "{$item},\n";
            } elseif (is_null($item)) {
                $string .= "null,\n";
            } elseif (is_array($item)) {
                $string .= "[array],\n";
            } elseif (is_object($item)) {
                $string .= "[" . get_class($item) . "],\n";
            } else {
                $string .= "[unknown],\n";
            }
        }
        return $string . ']';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_map(function ($item) {
            if (method_exists($item, 'toArray')) {
                return $item->toArray();
            }
            return $item;
        }, $this->items);
    }
    
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->count() === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->assertIsNotNull($offset, __METHOD__);
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        foreach ($this->items as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->items = unserialize($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->items = [];
    }

    /**
     * {@inheritdoc}
     */
    public function contains(...$values)
    {
        foreach ($values as $value) {
            if (!in_array($value, $this->items, true)) {
                return false;
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->items) ? $this->items[$key] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->assertIsNotNull($key, __METHOD__);
        $this->items[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(...$keys)
    {
        foreach ($keys as $key) {
            unset($this->items[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has(...$keys)
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->items)) {
                return false;
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function keys()
    {
        return new ArrayList(array_keys($this->items));
    }

    /**
     * {@inheritdoc}
     */
    public function values()
    {
        return new ArrayList(array_values($this->items));
    }

    /**
     * {@inheritdoc}
     */
    public function map(callable $handler)
    {
        $keys = array_keys($this->items);
        return new static(array_combine($keys, array_map($handler, $this->items, $keys)));
    }

    /**
     * {@inheritdoc}
     */
    public function reduce(callable $handler, $initial = null)
    {
        foreach ($this->items as $key => $item) {
            $initial = $handler($initial, $item, $key);
        }
        return $initial;
    }

    /**
     * @param mixed $value
     * @param string $method
     * @param int $order
     */
    private function assertIsNotNull($value, $method, $order = 1)
    {
        if (!isset($value)) {
            throw new InvalidArgumentException("Argument {$order} passed to {$method} must be not null");
        }
    }
}
