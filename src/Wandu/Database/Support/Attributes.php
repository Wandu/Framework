<?php
namespace Wandu\Database\Support;

trait Attributes
{
    /** @var array */
    protected $attributes = [];

    /**
     * @param string $name
     * @param array $arguments
     * @return static
     */
    public function __call($name, array $arguments = [])
    {
        $length = count($arguments);
        $this->attributes[Helper::camelCaseToUnderscore($name)] =
            $length ? $length === 1 ? $arguments[0] : $arguments : true;
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * @param string $name
     * @param string $value
     */
    function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->attributes);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}
