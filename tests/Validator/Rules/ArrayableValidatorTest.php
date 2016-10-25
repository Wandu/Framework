<?php
namespace Wandu\Validator\Rules;

use ArrayObject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class ArrayableValidatorTest extends ValidatorTestCase
{
    public function testValidate()
    {
        static::assertTrue(validator()->arrayable()->validate(null));
        static::assertTrue(validator()->arrayable()->validate([]));
        static::assertTrue(validator()->arrayable()->validate(new ArrayObject([])));
        static::assertTrue(validator()->arrayable()->validate(new Collection([]))); // also use Laravel's Collection.
        static::assertTrue(validator()->arrayable()->validate(new Tester([]))); // also use Laravel's Model

        static::assertFalse(validator()->arrayable()->validate((object)[]));
        static::assertFalse(validator()->arrayable()->validate("30"));

        static::assertTrue(validator()->arrayable([
            'age' => 'integer',
        ])->validate(new ArrayObject(['age' => 30])));

        // ignore other key 
        static::assertTrue(validator()->arrayable([
            'age' => 'integer',
        ])->validate(new ArrayObject(['age' => 30, 'other' => 'other...'])));

        static::assertFalse(validator()->arrayable([
            'age' => 'integer',
        ])->validate(new ArrayObject(['age' => "age string"])));

        static::assertTrue(validator()->arrayable([
            'wrong' => 'integer',
        ])->validate(new ArrayObject([])));
        static::assertFalse(validator()->arrayable([
            'wrong' => 'required|integer',
        ])->validate(new ArrayObject([])));
    }

    public function testAssertMethod()
    {
        $validator = validator()->arrayable(['name' => 'string', 'age' => 'integer',]);

        // valid
        $validator->assert(new ArrayObject([
            'name' => 'wandu',
            'age' => 30,
        ]));

        static::assertInvalidValueException(function () use ($validator) {
            $validator->assert('string');
        }, [
            'arrayable',
        ]);

        $validator->assert(new ArrayObject([]));
        $validator->assert(new ArrayObject([
            'age' => 30
        ]));
    }

    public function testAssertArrayOfArray()
    {
        $validator = validator()->arrayable([
            'name' => 'string',
            'company' => [
                'name' => 'string',
                'age' => 'integer',
            ],
        ]);

        $validator->assert(new ArrayObject([
            'name' => 'name string',
            'company' => [
                'name' => 'string',
                'age' => 38
            ],
        ]));

        static::assertInvalidValueException(function () use ($validator) {
            $validator->assert('string');
        }, [
            'arrayable',
        ]);

        $validator->assert(new ArrayObject());
        $validator->assert(new ArrayObject([
            'company' => [],
        ]));
    }

    public function testAssertAndValidatorOfArray()
    {
        $validator = validator()->arrayable([
            'name' => 'string|length_min:5',
        ]);

        static::assertInvalidValueException(function () use ($validator) {
            $validator->assert(new ArrayObject([
                'name' => '1234'
            ]));
        }, [
            'length_min:5@name',
        ]);
    }
}

class Tester extends Model 
{
    protected $fillable = [
        'name',
    ];
}