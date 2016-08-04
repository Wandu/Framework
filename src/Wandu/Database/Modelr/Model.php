<?php
namespace Wandu\Database\Modelr;

use ArrayAccess;

abstract class Model implements ArrayAccess
{
    /** @var array */
    protected static $defaults = [];
    
    /** @var array */
    protected $attributes = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }
}
