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

        static::assertTrue(validator()->array([
            'wrong' => 'integer',
        ])->validate([]));
        static::assertFalse(validator()->array([
            'wrong' => 'required|integer',
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
    
    public function testArrayWithMultiEmptyArray()
    {
        $validator = validator()->array([
            'username' => 'required|reg_exp:/^[a-zA-Z0-9_-]{4,30}$/',
            'name' => 'length_max:16',
            'profile' => validator()->pipeline(['required', [
                'image' => 'required|length_max:64',
                'alt' => 'string',
            ]]),
            'driver' => [
                'company' => 'required|length_max:16',
                'description' => 'string',
            ],
            'company' => [
                'registration' => 'required|length_max:16',
                'description' => 'string',
            ],
        ]);

        // all empty
        $validator->assert(null);
        $validator->assert('');
        
        // only array
        $validator->assert([
            'username' => 'wan2land',
            'profile' => ['image' => 'static/images/000.png',],
        ]);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([]);
        }, [
            'required@username',
            'required@profile',
        ]);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'username' => 'wan2land',
                'profile' => null,
            ]);
        }, [
            'required@profile',
        ]);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'username' => 'wan2land',
                'profile' => [],
            ]);
        }, [
            'required@profile.image',
        ]);
        
        // with driver
        $validator->assert([
            'username' => 'wan2land',
            'profile' => ['image' => 'static/images/000.png',],
            'driver' => null,
        ]);
        $validator->assert([
            'username' => 'wan2land',
            'profile' => ['image' => 'static/images/000.png',],
            'driver' => '',
        ]);
        $validator->assert([
            'username' => 'wan2land',
            'profile' => ['image' => 'static/images/000.png',],
            'driver' => ['company' => 'something'],
        ]);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'username' => 'wan2land',
                'profile' => ['image' => 'static/images/000.png',],
                'driver' => [],
            ]);
        }, [
            'required@driver.company',
        ]);

        // with company
        $validator->assert([
            'username' => 'wan2land',
            'profile' => ['image' => 'static/images/000.png',],
            'company' => null,
        ]);
        $validator->assert([
            'username' => 'wan2land',
            'profile' => ['image' => 'static/images/000.png',],
            'company' => '',
        ]);
        $validator->assert([
            'username' => 'wan2land',
            'profile' => ['image' => 'static/images/000.png',],
            'company' => ['registration' => '000-1234-1234'],
        ]);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert([
                'username' => 'wan2land',
                'profile' => ['image' => 'static/images/000.png',],
                'company' => [],
            ]);
        }, [
            'required@company.registration',
        ]);
    }
}
