<?php
namespace Wandu\Validator\Throwable;

use RuntimeException;
use Wandu\Validator\Contracts\ErrorThrowable;

class ErrorThrower implements ErrorThrowable
{
    /**
     * @param string $type
     * @param array $keys
     */
    public function throws(string $type, array $keys = [])
    {
        throw new RuntimeException();
    }
}
