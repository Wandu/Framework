<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class AndValidatorTest extends ValidatorTestCase
{
    public function testNothingAnd()
    {
        $validator = validator()->and();

        $validator->assert("always");
        $validator->assert(true);
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
