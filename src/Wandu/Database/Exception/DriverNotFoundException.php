<?php
namespace Wandu\Database\Exception;

use RuntimeException;

class DriverNotFoundException extends RuntimeException
{
    const CODE_UNDEFINED = 1;
    const CODE_UNSUPPORTED = 2;

    /**
     * @param string $driver
     */
    public function __construct($driver = null)
    {
        if ($driver) {
            $this->code = static::CODE_UNSUPPORTED;
            $this->message = "\"{$driver}\" is not supported.";
        } else {
            $this->code = static::CODE_UNDEFINED;
            $this->message = "driver is not defined.";
        }
    }
}
