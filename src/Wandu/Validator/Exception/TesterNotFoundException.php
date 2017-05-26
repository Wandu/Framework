<?php
namespace Wandu\Validator\Exception;

use RuntimeException;

class TesterNotFoundException extends RuntimeException
{
    /** @var string */
    protected $name;
    
    public function __construct($name)
    {
        $this->name = $name;
        parent::__construct("tester \"{$name}\" not found.");
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
