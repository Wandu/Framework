<?php
namespace Wandu\Validator;

use PHPUnit_Framework_TestCase;

class RulesTest extends PHPUnit_Framework_TestCase
{
    public function testInteger()
    {
        $this->assertTrue(validator()->integer()->validate(30));
        $this->assertFalse(validator()->integer()->validate("30"));
    }
    
    public function testArray()
    {
        $this->assertTrue(validator()->array()->validate([]));
        $this->assertFalse(validator()->array()->validate("30"));

        $this->assertTrue(validator()->array([
            'age' => 'integer',
        ])->validate(['age' => 30]));

        // ignore other key 
        $this->assertTrue(validator()->array([
            'age' => 'integer',
        ])->validate(['age' => 30, 'other' => 'other...']));

        $this->assertFalse(validator()->array([
            'age' => 'integer',
        ])->validate(['age' => "age string"]));
    }
}
