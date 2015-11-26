<?php
namespace Wandu\DI\Exception;

class CannotInjectException extends DIException
{
    /** @var string */
    protected $property;

    /**
     * @param string $class
     * @param string $property
     */
    public function __construct($class, $property)
    {
        parent::__construct($class);
        $this->property = $property;
        $this->message = "It cannot be injected; {$class}::\${$property}";
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }
}
