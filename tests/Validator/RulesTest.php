<?php
namespace Wandu\Validator;

use PHPUnit_Framework_TestCase;
use Wandu\Validator\Exception\InvalidValueException;

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

        $this->assertFalse(validator()->array([
            'wrong' => 'integer',
        ])->validate([]));
    }
    
    public function testArrayWithAssertMethod()
    {
        $arrayValidator = validator()->array(['name' => 'string', 'age' => 'integer',]);

        // valid
        $arrayValidator->assert([
            'name' => 'wandu',
            'age' => 30,
        ]);

        try {
            $arrayValidator->assert('string');
        } catch (InvalidValueException $e) {
            $this->assertEquals('type.array', $e->getType());
        }

        // assert
        try {
            $arrayValidator->assert([]);
        } catch (InvalidValueException $e) {
            $this->assertEquals('type.array.attributes', $e->getType());

            $innerExceptions = $e->getExceptions();
            $this->assertEquals(2, count($innerExceptions));
            $this->assertEquals('type.string', $innerExceptions[0]->getType());
            $this->assertEquals('type.integer', $innerExceptions[1]->getType());
        }

        // assert stop on fail
        try {
            $arrayValidator->assert([], true);
        } catch (InvalidValueException $e) {
            $this->assertEquals('type.array.attributes', $e->getType());

            $innerExceptions = $e->getExceptions();
            $this->assertEquals(0, count($innerExceptions));
        }
    }
}
