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
            'test_custom:hello',
        ]);
    }
    
    public function testValidateWithOthers()
    {
        $validator = validator()->pipeline()->max(20)->push(new TestOverTenValidator());

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(10);
        }, [
            'test_custom:hello',
        ]);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(21);
        }, [
            'max:20',
        ]);
    }
}

class TestOverTenValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'test_custom:{{something}}';
    
    protected $something = "hello";
    
    public function test($item)
    {
        return $item > 10;
    }
}
