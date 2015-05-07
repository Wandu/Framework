<?php
namespace Wandu\DI;

use RuntimeException;

class CannotChangeException extends RuntimeException
{
    protected $message = 'You cannot change the data.';
}
