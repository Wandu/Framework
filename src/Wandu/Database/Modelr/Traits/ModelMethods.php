<?php
namespace Wandu\Database\Modelr\Traits;

trait ModelMethods
{
    /** @var array */
    protected static $defaults = [];

    /** @var array raw data from database */
    protected $attributes = [];

    /**
     * @param callable $handler
     * @return static[]|\Wandu\Database\Modelr\Contracts\CollectionInterface
     */
    public static function all(callable $handler = null)
    {
        
    }

    /**
     * @param callable|null $handler
     * @return static
     */
    public static function first(callable $handler = null)
    {
        
    }

    /**
     * @param array $attributes
     * @return static|static[]|\Wandu\Database\Modelr\Contracts\CollectionInterface
     */
    public static function hydrate(array $attributes = [])
    {
        
    }

    public static function query()
    {
        
    }

    public function save()
    {
        
    }

    public function delete()
    {
        
    }

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