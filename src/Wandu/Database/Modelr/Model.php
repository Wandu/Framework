<?php
namespace Wandu\Database\Modelr;

use ArrayAccess;

class Model implements ArrayAccess
{
    /** @var array */
    protected static $defaults = [];
    
    /** @var array raw data from database */
    protected $attributes = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }
}
