<?php
namespace Wandu\Database\Modelr\Contracts;

use ArrayAccess;

/**
 * @see Wandu\Database\Modelr\Traits\ModelMethods
 */
interface ModelInterface extends ArrayAccess 
{
    /**
     * @param callable $handler
     * @return static[]|\Wandu\Database\Modelr\Contracts\CollectionInterface
     */
    public static function all(callable $handler = null);

    /**
     * @param callable|null $handler
     * @return static
     */
    public static function first(callable $handler = null);

    /**
     * @param array $attributes
     * @return static|static[]|\Wandu\Database\Modelr\Contracts\CollectionInterface
     */
    public static function hydrate(array $attributes = []);
    
    public static function query();
    
    public function save();
    
    public function delete();
    
    public function fill(array $attributes = []);
}
