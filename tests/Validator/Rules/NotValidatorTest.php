<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class NotValidatorTest extends ValidatorTestCase
{
    public function testNextAssert()
    {
        $validator = validator()->not(validator()->integer());

        $validator->assert('1');
        $validator->assert(false);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(0);
        }, [
            'not.integer' => ['it must be not the integer'],
        ]);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(111);
        }, [
            'not.integer' => ['it must be not the integer'],
        ]);
    }

    public function testNextValidate()
    {
        $validator = validator()->not(validator()->integer());

        $this->assertFalse($validator->validate(0));
        $this->assertFalse($validator->validate(111));

        $this->assertTrue($validator->validate('1'));
        $this->assertTrue($validator->validate(false));
    }
}
