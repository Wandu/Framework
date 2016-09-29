<?php
namespace Wandu\Validator\Rules;

use stdClass;
use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class RegExpValidatorTest extends ValidatorTestCase
{
    public function testRequired()
    {
        $validator = validator()->regExp("/^hello_world$/");

        $validator->assert("hello_world");

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert("other string");
        }, ['reg_exp:/^hello_world$/']);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(new stdClass);
        }, ['reg_exp:/^hello_world$/']);
    }
}
