<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class OrValidatorTest extends ValidatorTestCase
{
    public function testValidate()
    {
        $validator = validator()->or();

        static::assertFalse($validator->validate("always"));
        static::assertFalse($validator->validate(false));

        $validator = validator()->or([
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
        ]);

        static::assertTrue($validator->validate(null));

        $validator = validator()->or([
            validator()->alwaysFalse(),
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
        ]);

        static::assertTrue($validator->validate(null));

        $validator = validator()->or([
            validator()->alwaysFalse(),
            validator()->alwaysFalse(),
            validator()->alwaysFalse(),
        ]);

        static::assertFalse($validator->validate(null));
    }

    public function testAssert()
    {
        $validator = validator()->or();
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert("always");
        }, []);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(false);
        }, []);

        $validator = validator()->or([
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
        ]);

        $validator->assert(null);

        $validator = validator()->or([
            validator()->alwaysFalse(),
            validator()->alwaysTrue(),
            validator()->alwaysTrue(),
        ]);

        $validator->assert(null);

        $validator = validator()->or([
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
}
