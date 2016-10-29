<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class PipelineValidatorTest extends ValidatorTestCase
{
    public function testValidate()
    {
        $validator = validator()->pipeline();

        static::assertTrue($validator->validate("always"));
        static::assertTrue($validator->validate(true));

        $validator = validator()->pipeline([
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
        ]);

        static::assertTrue($validator->validate(''));

        $validator = validator()->pipeline([
            validator()->alwaysFalse(),
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
        ]);

        static::assertFalse($validator->validate(''));

        $validator = validator()->pipeline([
            validator()->alwaysFalse(),
            validator()->alwaysFalse(),
            validator()->alwaysFalse(),
        ]);

        static::assertFalse($validator->validate(''));
    }

    public function testAssert()
    {
        $validator = validator()->pipeline();

        $validator->assert("always");
        $validator->assert(true);

        $validator = validator()->pipeline([
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
        ]);

        $validator->assert(null);

        $validator = validator()->pipeline([
            validator()->alwaysFalse(),
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert('');
        }, [
            'always_false',
        ]);

        $validator = validator()->pipeline([
            validator()->alwaysFalse(),
            validator()->alwaysFalse(),
            validator()->alwaysFalse(),
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert('');
        }, [
            'always_false',
            'always_false',
            'always_false',
        ]);
    }
    
    public function testMinAndMax()
    {
        $validator = validator()->pipeline([
            validator()->min(10),
            validator()->max(100),
        ]);

        $validator->assert(10);
        $validator->assert(11);
        $validator->assert(50);
        $validator->assert(99);
        $validator->assert(100);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(9);
        }, [
            'min:10',
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(101);
        }, [
            'max:100',
        ]);
    }

    public function testMinAndMin()
    {
        $validator = validator()->pipeline([
            validator()->min(10),
            validator()->min(30),
        ]);

        $validator->assert(30);
        $validator->assert(31);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(9);
        }, [
            'min:10',
            'min:30',
        ]);
    }
}
