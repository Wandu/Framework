<?php
namespace Wandu\Modelr;

use ArrayAccess;

abstract class Model implements ArrayAccess
{
    /** @var Repository */
    protected $repository;

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array */
    protected $defaults = [];

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository = null)
    {
        $this->items = $this->defaults;
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @param array $valuesFromArray
     * @return self
     */
    public function fromArray($valuesFromArray)
    {
        $this->items = $valuesFromArray;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
}