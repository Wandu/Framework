<?php
namespace Wandu\Compiler\Math;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class Set implements Countable, IteratorAggregate
{
    /** @var array */
    protected $items;

    /**
     * Set constructor.
     * @param ...$items
     */
    public function __construct(...$items)
    {
        $this->items = $this->arrayUnique($items);
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
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @param mixed $item
     */
    public function insert($item)
    {
        if (!in_array($item, $this->items, true)) {
            $this->items[] = $item;
        }
    }

    /**
     * @param mixed $item
     * @return bool
     */
    public function has($item)
    {
        return in_array($item, $this->items, true);
    }

    /**
     * @param \Wandu\Compiler\Math\Set $other
     * @return \Wandu\Compiler\Math\Set
     */
    public function ringSum(Set $other)
    {
        if (!in_array(null, $this->items, true)) {
            return new Set(...$this->items);
        }

        $unionSet = $this->arrayUnique(array_merge($this->items, $other->items));
        if (in_array(null, $other->items, true)) {
            return new Set(...$unionSet);
        }

        return new Set(...array_filter($unionSet));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * @param \Wandu\Compiler\Math\Set $other
     * @return bool
     */
    public function equal(Set $other)
    {
        foreach ($this->items as $item) {
            if (!in_array($item, $other->items, true)) {
                return false;
            }
        }
        foreach ($other->items as $item) {
            if (!in_array($item, $this->items, true)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param \Wandu\Compiler\Math\Set $other
     */
    public function union(Set $other)
    {
        $this->items = $this->arrayUnique(array_merge($this->items, $other->items));
    }

    protected function arrayUnique(array $array)
    {
        $arrayToReturn = [];
        foreach ($array as $item) {
            if (!in_array($item, $arrayToReturn, true)) {
                $arrayToReturn[] = $item;
            }
        }
        return $arrayToReturn;
    }
}
