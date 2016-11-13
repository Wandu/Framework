<?php
namespace Wandu\Database\Exception;

use RuntimeException;

class IdentifierNotFoundException extends RuntimeException
{
    public function __construct()
    {
        $this->message = "Identifier not found from entity";
    }
}
