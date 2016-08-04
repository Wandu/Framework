<?php
namespace Wandu\Validator\Exception;

use RuntimeException;

class InvalidValueException extends RuntimeException
{
    /** @var \Wandu\Validator\Exception\InvalidValueException[] */
    protected $exceptions = [];
    
    /** @var string */
    protected $type;

    /**
     * @param string $type
     * @param string $message
     * @param \Wandu\Validator\Exception\InvalidValueException[] $exceptions
     */
    public function __construct($type, $message = '', array $exceptions = [])
    {
        $this->type = $type;
        $this->message = $message;
        $this->exceptions = $exceptions;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param \Wandu\Validator\Exception\InvalidValueException $exception
     * @return self
     */
    public function insertException(InvalidValueException $exception)
    {
        $this->exceptions[] = $exception;
        return $this;
    }

    /**
     * @return \Wandu\Validator\Exception\InvalidValueException[]
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }
}
