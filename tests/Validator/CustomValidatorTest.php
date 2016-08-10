<?php
namespace Wandu\Validator;

use Wandu\Validator\Rules\ValidatorAbstract;

class CustomValidatorTest extends ValidatorTestCase
{
    public function testValidate()
    {
        $validator = new TestOverTenValidator();

        $validator->assert(11);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(10);
        }, [
            'test.custom' => ['it must be larger than 10'],
        ]);
    }
//    
//    public function testValidateWithOthers()
//    {
//        $validator = new TestOverTenValidator();
//        $validator = $validator->max(20);
//
//        $this->assertInvalidValueException(function () use ($validator) {
//            $validator->assert(10);
//        }, [
//            'test.custom' => ['it must be larger than 10'],
//        ]);
//        $this->assertInvalidValueException(function () use ($validator) {
//            $validator->assert(21);
//        }, [
//            'max' => ['it must be less or equal than 20'],
//        ]);
//    }
}

class TestOverTenValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'test.custom';
    const ERROR_MESSAGE = 'it must be larger than 10';
    
    public function test($item)
    {
        return $item > 10;
    }
}
