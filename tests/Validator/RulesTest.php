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
    
    public function testOptional()
    {
        $validator = validator()->optional();
        
        $this->assertTrue($validator->validate(null));
        $this->assertTrue($validator->validate(''));

        $this->assertFalse($validator->validate('1'));
        $this->assertFalse($validator->validate(false));
        $this->assertFalse($validator->validate(0));
    }

    public function testOptionalWithOthers()
    {
        $validator = validator()->optional(validator()->integer());

        $this->assertTrue($validator->validate(null));
        $this->assertTrue($validator->validate(''));

        $this->assertTrue($validator->validate(0)); // true
        $this->assertTrue($validator->validate(111)); // true
        
        $this->assertFalse($validator->validate('1'));
        $this->assertFalse($validator->validate(false));
    }

    public function testOptionalWithChaining()
    {
        $validator = validator()->optional()->integer();

        $this->assertTrue($validator->validate(null));
        $this->assertTrue($validator->validate(''));

        $this->assertTrue($validator->validate(0)); // true
        $this->assertTrue($validator->validate(111)); // true

        $this->assertFalse($validator->validate('1'));
        $this->assertFalse($validator->validate(false));
    }

    public function testOptionalAssert()
    {
        $validator = validator()->optional()->integer();

        $validator->assert(null);
        $validator->assert(10);
        
        try {
            $validator->assert('30');
        } catch (InvalidValueException $e) {
            $this->assertEquals('optional', $e->getType());

            $innerExceptions = $e->getExceptions();
            $this->assertEquals(1, count($innerExceptions));
            $this->assertEquals('type.integer', $innerExceptions[0]->getType());
        }

        try {
            $validator->assert('30', true);
        } catch (InvalidValueException $e) {
            $this->assertEquals('optional', $e->getType());
            $this->assertEquals(0, count($e->getExceptions()));
        }
    }
}
