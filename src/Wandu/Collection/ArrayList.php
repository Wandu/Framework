<?php
namespace Wandu\Collection;

use InvalidArgumentException;
use Traversable;
use Wandu\Collection\Contracts\ListInterface;

class ArrayList implements ListInterface
{
    /** @var \Traversable */
    protected $iterator;

    /** @var array */
    protected $items;

    /**
     * @param array|\Traversable $items
     */
    public function __construct($items = [])
    {
        if ($items instanceof Traversable) {
            $this->iterator = $items; // for lazy iterate
        } else {
            $this->items = array_values($items);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $string = static::class . " [\n";
        foreach ($this as $item) {
            $string .= "    ";
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
        $arr = [];
        foreach ($this as $item) {
            if (method_exists($item, 'toArray')) {
                $arr[] = $item->toArray();
            } else {
                $arr[] = $item;
            }
        }
        return $arr;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $this->executeIterator();
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $this->executeIterator();
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    function jsonSerialize()
    {
        return $this->all(); // safe
    }
    
    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        if (isset($this->iterator)) {
            $this->items = [];
            foreach ($this->iterator as $item) {
                yield $this->items[] = $item;
            }
            $this->iterator = null;
            return;
        }
        foreach ($this->items as $item) {
            yield $item;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $this->executeIterator();
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
        $this->assertIsNullOrIntegerLessSize($offset, __METHOD__);
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
    public function serialize()
    {
        $this->executeIterator();
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
        $this->iterator = null;
        $this->items = [];
    }

    /**
     * {@inheritdoc}
     */
    public function contains(...$values)
    {
        $this->executeIterator();
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
        $this->executeIterator();
        return array_key_exists($key, $this->items) ? $this->items[$key] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->executeIterator();
        if (isset($key)) {
            $this->items[$key + 0] = $value;
        } else {
            $this->items[] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(...$keys)
    {
        $this->executeIterator();
        foreach ($keys as $key) {
            unset($this->items[$key]);
        }
        $this->items = array_values($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function has(...$keys)
    {
        $this->executeIterator();
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
    public function filter(callable $handler = null)
    {
        $this->executeIterator();
        if ($handler) {
            return new ArrayList(array_values(array_filter($this->items, $handler, ARRAY_FILTER_USE_BOTH)));
        }
        return new ArrayList(array_values(array_filter($this->items)));
    }

    /**
     * {@inheritdoc}
     */
    public function map(callable $handler)
    {
        $this->executeIterator();
        return new ArrayList(array_map($handler, $this->items, array_keys($this->items)));
    }

    /**
     * {@inheritdoc}
     */
    public function reduce(callable $handler, $initial = null)
    {
        foreach ($this as $key => $item) {
            $initial = $handler($initial, $item, $key);
        }
        return $initial;
    }

    /**
     * {@inheritdoc}
     */
    public function groupBy(callable $handler)
    {
        $new = [];
        foreach ($this as $key => $item) {
            $groupName = call_user_func($handler, $item, $key);
            if (!isset($new[$groupName])) {
                $new[$groupName] = new ArrayList();
            }
            $new[$groupName][] = $item;
        }
        return new HashMap($new);
    }

    /**
     * {@inheritdoc}
     */
    public function keyBy(callable $handler)
    {
        $new = [];
        foreach ($this as $key => $item) {
            $keyName = call_user_func($handler, $item, $key);
            $new[$keyName] = $item;
        }
        return new HashMap($new);
    }

    /**
     * {@inheritdoc}
     */
    public function combine(ListInterface $list)
    {
        $this->executeIterator();
        return new HashMap(array_combine($this->items, $list->all()));
    }

    /**
     * {@inheritdoc}
     */
    public function first(callable $handler = null, $default = null)
    {
        $this->executeIterator();
        if ($handler) {
            foreach ($this->items as $key => $item) {
                if (call_user_func($handler, $item, $key)) {
                    return $item;
                }
            }
            return $default;
        }
        return isset($this->items[0]) ? $this->items[0] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function last(callable $handler = null, $default = null)
    {
        $this->executeIterator();
        if ($handler) {
            $length = count($this->items);
            for ($i = 0; $i < $length; $i++) {
                $key = $length - $i - 1;
                $item = $this->items[$key];
                if (call_user_func($handler, $item, $key)) {
                    return $item;
                }
            }
            return $default;
        }
        return ($length = count($this->items)) ? $this->items[$length - 1] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function intersect(ListInterface $list)
    {
        $this->executeIterator();
        return new ArrayList(array_intersect($this->items, $list->all()));
    }

    /**
     * {@inheritdoc}
     */
    public function union(ListInterface $list)
    {
        $this->executeIterator();
        return new ArrayList(array_unique_union($this->items, $list->all()));
    }

    /**
     * {@inheritdoc}
     */
    public function merge(ListInterface $list)
    {
        $this->executeIterator();
        return new ArrayList(array_merge($this->items, $list->all()));
    }

    /**
     * {@inheritdoc}
     */
    public function implode($glue = null)
    {
        $this->executeIterator();
        return implode($glue, $this->items);
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
    public function pop()
    {
        $this->executeIterator();
        return array_pop($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function push(...$values)
    {
        $this->executeIterator();
        $this->items = array_merge($this->items, $values);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function shift()
    {
        $this->executeIterator();
        return array_shift($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function unshift(...$values)
    {
        $this->executeIterator();
        $this->items = array_merge(array_reverse($values), $this->items);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function reverse()
    {
        $this->executeIterator();
        return new ArrayList(array_reverse($this->items));
    }

    /**
     * {@inheritdoc}
     */
    public function shuffle()
    {
        $this->executeIterator();
        $items = $this->items;
        shuffle($items);
        return new ArrayList($items);
    }

    /**
     * {@inheritdoc}
     */
    public function sort(callable $callback = null)
    {
        $this->executeIterator();
        $items = $this->items;
        if ($callback) {
            usort($items, $callback);
        } else {
            sort($items);
        }
        return new ArrayList($items);
    }
    
    /**
     * {@inheritdoc}
     */
    public function slice($offset, $length = null)
    {
        $this->executeIterator();
        return new ArrayList(array_slice($this->items, $offset, $length));
    }

    /**
     * {@inheritdoc}
     */
    public function splice($offset, $length = null, $replacement = null)
    {
        $this->executeIterator();
        if ($length) {
            return new ArrayList(array_splice($this->items, $offset, $length, $replacement));
        }
        return new ArrayList(array_splice($this->items, $offset));
    }
    
    /**
     * {@inheritdoc}
     */
    public function unique()
    {
        $this->executeIterator();
        return new ArrayList(array_unique($this->items));
    }
    
    /**
     * @param mixed $value
     * @param string $method
     * @param int $order
     */
    private function assertIsNullOrIntegerLessSize($value, $method, $order = 1)
    {
        if (!isset($value)) {
            return;
        }
        if (is_int($value) && $value <= $this->count()) {
            return;
        }
        if (is_string($value)) {
            if ($value == (($value + 0) . '') && $value <= $this->count()) {
                return;
            }
        }
        throw new InvalidArgumentException("Argument {$order} passed to {$method} must be null or an integer less than the size of the list");
    }
    
    private function executeIterator()
    {
        if (isset($this->iterator)) {
            $this->items = iterator_to_array($this->iterator);
            $this->iterator = null;
        }
    }
}
