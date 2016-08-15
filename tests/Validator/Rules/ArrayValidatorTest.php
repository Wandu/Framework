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
            'exists@name',
            'exists@age',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([]);
        }, [
            'exists@name',
            'exists@age',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'age' => 30
            ]);
        }, [
            'exists@name',
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
            'exists@name',
            'exists@company',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([]);
        }, [
            'exists@name',
            'exists@company',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'company' => [],
            ]);
        }, [
            'exists@name',
            'exists@company.name',
            'exists@company.age',
        ]);
    }
}
