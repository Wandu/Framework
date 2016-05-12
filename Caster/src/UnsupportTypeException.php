<?php
namespace Wandu\Caster;

use Exception;
use RuntimeException;

class UnsupportTypeException extends RuntimeException
{
    /** @var string */
    protected $type;

    public function __construct($type, $code = 0, Exception $previous = null)
    {
        $this->type = $type;
        $message = "unsupport type \"{$type}\".";
        parent::__construct($message, $code, $previous);
    }
}
