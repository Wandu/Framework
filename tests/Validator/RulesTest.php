<?php
namespace Wandu\Validator;

use PHPUnit_Framework_TestCase;
use Wandu\Validator\Exception\InvalidValueException;

class RulesTest extends PHPUnit_Framework_TestCase
{
    public function testInteger()
    {
        validator()->integer()->assert(30);
        $this->assertInvalidValueException(function () {
            validator()->integer()->assert("30");
        }, 'integer', 'it must be the integer');
    }

    public function testString()
    {
        validator()->string()->assert("30");
        $this->assertInvalidValueException(function () {
            validator()->string()->assert(30);
        }, 'string', 'it must be the string');
    }
    
    public function testMin()
    {
        validator()->min(5)->assert(100);
        validator()->min(5)->assert(6);
        validator()->min(5)->assert(5);
        $this->assertInvalidValueException(function () {
            validator()->min(5)->assert(4);
        }, 'min', 'it must be greater or equal than 5');
    }

    public function testMax()
    {
        validator()->max(5)->assert(0);
        validator()->max(5)->assert(4);
        validator()->max(5)->assert(5);

        $this->assertInvalidValueException(function () {
            validator()->max(5)->assert(6);
        }, 'max', 'it must be less or equal than 5');
    }
    
    protected function assertInvalidValueException(callable $closure, $type, $message)
    {
        try {
            call_user_func($closure);
            $this->fail();
        } catch (InvalidValueException $e) {
            $this->assertEquals($type, $e->getType());
            $this->assertEquals($message, $e->getMessage());
        }
    }
}
