<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class AndValidatorTest extends ValidatorTestCase
{
    public function testValidate()
    {
        $validator = validator()->and();

        static::assertTrue($validator->validate("always"));
        static::assertTrue($validator->validate(true));

        $validator = validator()->and([
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
        ]);

        static::assertTrue($validator->validate(null));

        $validator = validator()->and([
            validator()->alwaysFalse(),
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
        ]);

        static::assertFalse($validator->validate(null));

        $validator = validator()->and([
            validator()->alwaysFalse(),
            validator()->alwaysFalse(),
            validator()->alwaysFalse(),
        ]);

        static::assertFalse($validator->validate(null));
    }

    public function testAssert()
    {
        $validator = validator()->and();

        $validator->assert("always");
        $validator->assert(true);

        $validator = validator()->and([
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
        ]);

        $validator->assert(null);

        $validator = validator()->and([
            validator()->alwaysFalse(),
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(null);
        }, [
            'always_false',
        ]);

        $validator = validator()->and([
            validator()->alwaysFalse(),
            validator()->alwaysFalse(),
            validator()->alwaysFalse(),
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(null);
        }, [
            'always_false',
            'always_false',
            'always_false',
        ]);
    }
    
    public function testMinAndMax()
    {
        $validator = validator()->and([
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
        $validator = validator()->and([
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
