<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class ArrayValidatorTest extends ValidatorTestCase
{
    public function testValidate()
    {
        $this->assertTrue(validator()->array()->validate([]));
        $this->assertFalse(validator()->array()->validate((object)[]));
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

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert('string');
        }, [
            'array',
            'array_attribute@name',
            'array_attribute@age',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([]);
        }, [
            'array_attribute@name',
            'array_attribute@age',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'age' => 30
            ]);
        }, [
            'array_attribute@name',
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

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert('string');
        }, [
            'array',
            'array_attribute@name',
            'array_attribute@company',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([]);
        }, [
            'array_attribute@name',
            'array_attribute@company',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'company' => [],
            ]);
        }, [
            'array_attribute@name',
            'array_attribute@company.name',
            'array_attribute@company.age',
        ]);
    }

    public function testAssertAndValidatorOfArray()
    {
        $validator = validator()->array([
            'name' => 'string&&length_min:5',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'name' => '1234'
            ]);
        }, [
            'length_min:5@name',
        ]);
    }
}
