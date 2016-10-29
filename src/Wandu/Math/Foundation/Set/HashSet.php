<?php
namespace Wandu\Math\Foundation\Set;

use InvalidArgumentException;
use Wandu\Math\Foundation\SetInterface;
use ArrayIterator;

class HashSet implements SetInterface
{
    /** @var array */
    protected $scalars = [];

    /** @var array */
    protected $objects = [];

    /**
     * @param array $items
     */
	public function __construct(array $items)
	{
        foreach ($items as $item) {
            $this->add($item);
        }
	}

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->scalars) + count($this->objects);
    }

    /**
     * {@inheritdoc}
     */
    public function equal(SetInterface $other)
    {
        if (!$other instanceof HashSet) {
            throw new InvalidArgumentException('unsupported type of Set.');
        }
        if (count($other->scalars) !== count($this->scalars)) {
            return false;
        }
        if (count($other->objects) !== count($this->objects)) {
            return false;
        }
        foreach ($this->scalars as $key => $item) {
            if (!array_key_exists($key, $other->scalars)) {
                return false;
            }
        }
        foreach ($other->scalars as $key => $item) {
            if (!array_key_exists($key, $this->scalars)) {
                return false;
            }
        }
        foreach ($this->objects as $key => $item) {
            if (!array_key_exists($key, $other->objects)) {
                return false;
            }
        }
        foreach ($other->objects as $key => $item) {
            if (!array_key_exists($key, $this->objects)) {
                return false;
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function intersection(SetInterface $other)
    {
        if (!$other instanceof HashSet) {
            throw new InvalidArgumentException('unsupported type of Set.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function union(SetInterface $other)
    {
        if (!$other instanceof HashSet) {
            throw new InvalidArgumentException('unsupported type of Set.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function difference(SetInterface $other)
    {
        if (!$other instanceof HashSet) {
            throw new InvalidArgumentException('unsupported type of Set.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($item)
    {
        if (is_scalar($item)) {
            return array_key_exists(gettype($item) . $item, $this->scalars);
        }
        if (is_object($item)) {
            return array_key_exists(spl_object_hash($item), $this->objects);
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function add($item)
    {
        if (is_scalar($item)) {
            return $this->scalars[gettype($item) . $item] = $item;
        }
        if (is_object($item)) {
            return $this->objects[spl_object_hash($item)] = $item;
        }
        throw new InvalidArgumentException(
            'unsupported value: ' . str_replace("\n", '', var_export($item, true))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function remove($item)
    {
        if (is_scalar($item)) {
            unset($this->scalars[gettype($item) . $item]);
        }
        if (is_object($item)) {
            unset($this->objects[spl_object_hash($item)]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator(array_merge(
            array_values($this->scalars),
            array_values($this->objects)
        ));
    }
}
