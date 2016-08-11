<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class ArrayValidatorTest extends ValidatorTestCase
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

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert('string');
        }, [
            'array' => ['it must be the array'],
            'name:string' => ['name must be the string'],
            'age:integer' => ['age must be the integer'],
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([]);
        }, [
            'name:string' => ['name must be the string'],
            'age:integer' => ['age must be the integer'],
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'age' => 30
            ]);
        }, [
            'name:string' => ['name must be the string'],
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
            'array' => ['it must be the array'],
            'name:string' => ['name must be the string'],
            'company:array' => ['company must be the array'],
            'company.name:string' => ['company.name must be the string'],
            'company.age:integer' => ['company.age must be the integer'],
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([]);
        }, [
            'name:string' => ['name must be the string'],
            'company:array' => ['company must be the array'],
            'company.name:string' => ['company.name must be the string'],
            'company.age:integer' => ['company.age must be the integer'],
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'company' => [],
            ]);
        }, [
            'name:string' => ['name must be the string'],
            'company.name:string' => ['company.name must be the string'],
            'company.age:integer' => ['company.age must be the integer'],
        ]);
    }
}
