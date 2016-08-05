<?php
namespace Wandu\Validator\Rules;

use PHPUnit_Framework_TestCase;
use Wandu\Validator\Exception\InvalidValueException;
use function Wandu\Validator\validator;

class ArrayValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testValidate()
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

    public function testAssertMethod()
    {
        $validator = validator()->array(['name' => 'string', 'age' => 'integer',]);

        // valid
        $validator->assert([
            'name' => 'wandu',
            'age' => 30,
        ]);

        try {
            $validator->assert('string');
        } catch (InvalidValueException $e) {
            $this->assertEquals('array', $e->getType());
        }

        // assert
        try {
            $validator->assert([]);
        } catch (InvalidValueException $e) {
            $this->assertEquals('array.attributes', $e->getType());

            $innerExceptions = $e->getExceptions();
            $this->assertEquals(2, count($innerExceptions));
            $this->assertEquals('string', $innerExceptions[0]->getType());
            $this->assertEquals('integer', $innerExceptions[1]->getType());
        }

        // assert stop on fail
        try {
            $validator->assert([], true);
        } catch (InvalidValueException $e) {
            $this->assertEquals('array.attributes', $e->getType());

            $innerExceptions = $e->getExceptions();
            $this->assertEquals(0, count($innerExceptions));
        }
    }
    
    public function testAssertArrayOfArray()
    {
        $validator = validator()->array([
            'name' => 'string',
            'company' => validator()->array([
                'name' => 'string',
                'age' => 'integer',
            ])
        ]);
        
        $validator->assert([
            'name' => 'name string',
            'company' => [
                'name' => 'string',
                'age' => 38
            ],
        ]);
        
        try {
            $validator->assert([]);
        } catch (InvalidValueException $e) {
            $innerExceptions = $e->getExceptions();
            $this->assertEquals(2, count($innerExceptions));
            $this->assertEquals('string', $innerExceptions[0]->getType());
            $this->assertEquals('array', $innerExceptions[1]->getType());
        }

        try {
            $validator->assert([
                'company' => [],
            ]);
        } catch (InvalidValueException $e) {
            $innerExceptions = $e->getExceptions();
            $this->assertEquals(2, count($innerExceptions));
            $this->assertEquals('string', $innerExceptions[0]->getType());
            $this->assertEquals('array.attributes', $innerExceptions[1]->getType());
            
            $innerInnerExceptions = $innerExceptions[1]->getExceptions();
            $this->assertEquals(2, count($innerInnerExceptions));
            $this->assertEquals('string', $innerInnerExceptions[0]->getType());
            $this->assertEquals('integer', $innerInnerExceptions[1]->getType());
        }
    }
}
