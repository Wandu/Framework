<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class ArrayValidatorTest extends ValidatorTestCase
{
    public function testValidate()
    {
        static::assertTrue(validator()->array()->validate(null));
        static::assertTrue(validator()->array()->validate([]));
        static::assertFalse(validator()->array()->validate((object)[]));
        static::assertFalse(validator()->array()->validate("30"));

        static::assertTrue(validator()->array([
            'age' => 'integer',
        ])->validate(['age' => 30]));

        // ignore other key 
        static::assertTrue(validator()->array([
            'age' => 'integer',
        ])->validate(['age' => 30, 'other' => 'other...']));

        static::assertFalse(validator()->array([
            'age' => 'integer',
        ])->validate(['age' => "age string"]));

        static::assertFalse(validator()->array([
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

        static::assertInvalidValueException(function () use ($validator) {
            $validator->assert('string');
        }, [
            'array',
        ]);

        $validator->assert([]);
        $validator->assert([
            'age' => 30
        ]);
    }
    
    public function testAssertArrayOfArray()
    {
        $validator = validator()->array([
            'name' => 'string',
            'company' => [
                'name' => 'string',
                'age' => 'integer',
            ],
        ]);
        
        $validator->assert([
            'name' => 'name string',
            'company' => [
                'name' => 'string',
                'age' => 38
            ],
        ]);

        static::assertInvalidValueException(function () use ($validator) {
            $validator->assert('string');
        }, [
            'array',
        ]);

        $validator->assert([]);
        $validator->assert([
            'company' => [],
        ]);
    }

    public function testAssertAndValidatorOfArray()
    {
        $validator = validator()->array([
            'name' => 'string|length_min:5',
        ]);

        static::assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'name' => '1234'
            ]);
        }, [
            'length_min:5@name',
        ]);
    }
}
