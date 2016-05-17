<?php
namespace Wandu\DI\Exception;

use RuntimeException;

class CannotFindParameterException extends RuntimeException
{
    /** @var string */
    protected $parameter;
    
    /**
     * @param string $parameter
     */
    public function __construct($parameter)
    {
        $this->parameter = $parameter;
        $this->message = "cannot find parameter \"{$parameter}\".";
    }

    /**
     * @return string
     */
    public function getParameter()
    {
        return $this->parameter;
    }
}
