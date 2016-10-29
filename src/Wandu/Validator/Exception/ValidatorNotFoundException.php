<?php
namespace Wandu\Validator\Exception;

use RuntimeException;

class ValidatorNotFoundException extends RuntimeException
{
    /** @var string */
    protected $name;
    
    public function __construct($name)
    {
        $this->name = $name;
        parent::__construct("validator \"{$name}\" not found.");
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
