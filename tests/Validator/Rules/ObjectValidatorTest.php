<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class ObjectValidatorTest extends ValidatorTestCase
{
    public function testValidate()
    {
        $this->assertTrue(validator()->object()->validate((object)[]));
        $this->assertFalse(validator()->object()->validate([]));
        $this->assertFalse(validator()->object()->validate("30"));

        $this->assertTrue(validator()->object([
            'age' => 'integer',
        ])->validate((object)['age' => 30]));

        // ignore other key 
        $this->assertTrue(validator()->object([
            'age' => 'integer',
        ])->validate((object)['age' => 30, 'other' => 'other...']));

        $this->assertFalse(validator()->object([
            'age' => 'integer',
        ])->validate((object)['age' => "age string"]));

        $this->assertFalse(validator()->object([
            'wrong' => 'integer',
        ])->validate((object)[]));
    }

    public function testAssertMethod()
    {
        $validator = validator()->object(['name' => 'string', 'age' => 'integer',]);

        // valid
        $validator->assert((object)[
            'name' => 'wandu',
            'age' => 30,
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert('string');
        }, [
            'object',
            'object_property@name',
            'object_property@age',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert((object)[]);
        }, [
            'object_property@name',
            'object_property@age',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert((object)[
                'age' => 30
            ]);
        }, [
            'object_property@name',
        ]);
    }

    public function testAssertArrayOfArray()
    {
        $validator = validator()->object([
            'name' => 'string',
            'company' => [
                'name' => 'string',
                'age' => 'integer',
            ],
        ]);

        $validator->assert((object)[
            'name' => 'name string',
            'company' => [
                'name' => 'string',
                'age' => 38
            ],
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert('string');
        }, [
            'object',
            'object_property@name',
            'object_property@company',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert((object)[]);
        }, [
            'object_property@name',
            'object_property@company',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert((object)[
                'company' => [],
            ]);
        }, [
            'object_property@name',
            'array_attribute@company.name',
            'array_attribute@company.age',
        ]);
    }

    public function testAssertAndValidatorOfArray()
    {
        $validator = validator()->object([
            'name' => 'string&&length_min:5',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert((object)[
                'name' => '1234'
            ]);
        }, [
            'length_min:5@name',
        ]);
    }
}
