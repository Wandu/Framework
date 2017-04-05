<?php
namespace Wandu\Support;

use InvalidArgumentException;
use ArrayAccess;

class DotArray implements ArrayAccess
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
    public function getRawData()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        if ($name === '') {
            return $this->items;
        }
        $names = explode('.', $name);
        $dataToReturn = $this->items;
        while (count($names)) {
            $name = array_shift($names);
            if (!is_array($dataToReturn) || !array_key_exists($name, $dataToReturn)) {
                return $default;
            }
            $dataToReturn = $dataToReturn[$name];
        }
        return $dataToReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        if ($name === '') {
            $this->items = $value;
        }
        $names = explode('.', $name);
        $dataToSet = &$this->items;
        while (count($names)) {
            $name = array_shift($names);
            if (!is_array($dataToSet)) {
                $dataToSet = [];
            }
            if (!array_key_exists($name, $dataToSet)) {
                $dataToSet[$name] = null;
            }
            $dataToSet = &$dataToSet[$name];
        }
        $dataToSet = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        if ($name === '') {
            return true;
        }
        $names = explode('.', $name);
        $dataToReturn = $this->items;
        while (count($names)) {
            $name = array_shift($names);
            if (!is_array($dataToReturn) || !array_key_exists($name, $dataToReturn)) {
                return false;
            }
            $dataToReturn = $dataToReturn[$name];
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        if ($name === '') {
            $this->items = [];
            return true;
        }
        $names = explode('.', $name);
        $dataToReturn = &$this->items;
        while (count($names)) {
            $name = array_shift($names);
            if (!is_array($dataToReturn) || !array_key_exists($name, $dataToReturn)) {
                return false;
            }
            if (count($names) === 0) {
                unset($dataToReturn[$name]);
            } else {
                $dataToReturn = &$dataToReturn[$name];
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function subset($name)
    {
        $subset = $this->get($name);
        if (!is_array($subset)) {
            throw new InvalidArgumentException('subset must be an array.');
        }
        return new static($subset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if (strpos($offset, '||') !== false) {
            list($offset, $default) = explode('||', $offset);
            return $this->get($offset, $default);
        }
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}
