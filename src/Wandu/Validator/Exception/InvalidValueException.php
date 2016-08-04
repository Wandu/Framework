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
     * @param string $type
     * @param string $message
     * @return self
     */
    public function setError($type, $message)
    {
        $this->exceptions[$type][] = $message;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->exceptions;
    }

    /**
     * @param string $type
     * @param string $message
     * @return bool
     */
    public function hasError($type, $message = null)
    {
        if (!array_key_exists($type, $this->exceptions) || count($this->exceptions[$type]) === 0) {
            return false;
        }
        if ($message) {
            return in_array($message, $this->exceptions[$type]);
        }
        return true;
    }
}
