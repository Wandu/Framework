<?php
namespace Wandu\Validator\Exception;

use RuntimeException;

class InvalidValueException extends RuntimeException
{
    /** @var array */
    protected $types = [];

    /**
     * @param array $types
     */
    public function __construct(array $types = [])
    {
        $this->types = $types;
        parent::__construct('invalid value.');
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }
}
