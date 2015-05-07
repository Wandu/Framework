<?php
namespace Wandu\DI;

use RuntimeException;

class NullReferenceException extends RuntimeException
{
    protected $message = 'You cannot access null reference container.';
}
