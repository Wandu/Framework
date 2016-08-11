<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class OptionalValidatorTest extends ValidatorTestCase
{
    public function testOptionalAssert()
    {
        $validator = validator()->optional();

        $validator->assert(null);
        $validator->assert('');

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert('1');
        }, [
            'optional',
        ]);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(false);
        }, [
            'optional',
        ]);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(0);
        }, [
            'optional',
        ]);
    }

    public function testOptionalValidate()
    {
        $validator = validator()->optional();

        $this->assertTrue($validator->validate(null));
        $this->assertTrue($validator->validate(''));

        $this->assertFalse($validator->validate('1'));
        $this->assertFalse($validator->validate(false));
        $this->assertFalse($validator->validate(0));
    }

    public function testNextAssert()
    {
        $validator = validator()->optional(validator()->integer());

        $validator->assert(null);
        $validator->assert('');
        
        $validator->assert(0);
        $validator->assert(111);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert('1');
        }, [
            'integer',
        ]);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(false);
        }, [
            'integer',
        ]);
    }

    public function testNextValidate()
    {
        $validator = validator()->optional(validator()->integer());

        $this->assertTrue($validator->validate(null));
        $this->assertTrue($validator->validate(''));

        $this->assertTrue($validator->validate(0));
        $this->assertTrue($validator->validate(111));
        
        $this->assertFalse($validator->validate('1'));
        $this->assertFalse($validator->validate(false));
    }
}
