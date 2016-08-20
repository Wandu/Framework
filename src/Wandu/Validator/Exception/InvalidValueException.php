<?php
namespace Wandu\Validator\Exception;

use RuntimeException;

class InvalidValueException extends RuntimeException
{
    /** @var array */
    protected $types = [];

    /**
     * @param string $type
     */
    public function __construct($type = null)
    {
        parent::__construct('invalid value.');
        if ($type) {
            $this->appendType($type);
        }
    }

    /**
     * @param string $type
     */
    public function appendType($type)
    {
        $this->types[] = $type;
    }

    /**
     * @param array $types
     */
    public function appendTypes(array $types = [])
    {
        $this->types = array_merge($this->types, $types);
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }
}
