<?php
namespace Wandu\Validator;

use PHPUnit_Framework_TestCase;
use Wandu\Validator\Exception\InvalidValueException;

class ValidatorTestCase extends PHPUnit_Framework_TestCase
{
    protected function assertInvalidValueException(callable $closure, $messages)
    {
        try {
            call_user_func($closure);
            $this->fail();
        } catch (InvalidValueException $e) {
            $this->assertEquals($messages, $e->getMessages());
        }
    }
}
