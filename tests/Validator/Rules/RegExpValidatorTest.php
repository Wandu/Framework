<?php
namespace Wandu\Validator\Rules;

use stdClass;
use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class RegExpValidatorTest extends ValidatorTestCase
{
    public function testRegExp()
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

    public function testRegExpFrom()
    {
        $validator = validator()->from("reg_exp:/^hello_world$/");

        $validator->assert("hello_world");

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert("other string");
        }, ['reg_exp:/^hello_world$/']);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(new stdClass);
        }, ['reg_exp:/^hello_world$/']);
    }

    public function testRegExpHasComma()
    {
        $validator = validator()->from("reg_exp:/^\\d{3,5}$/");

        $validator->assert("100");
        $validator->assert("1000");
        $validator->assert("10000");

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert("10");
        }, ['reg_exp:/^\\d{3,5}$/']);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert('100000');
        }, ['reg_exp:/^\\d{3,5}$/']);
    }
}
