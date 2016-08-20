<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\ValidatorNotFoundException;
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
        $validator = validator()->and([
            validator()->max(20),
            new TestOverTenValidator(),
        ]);

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

    public function testRegisterBaseNamespace()
    {
        $factory = new ValidatorFactory();
        try {
            $factory->testOverTen();
            $this->fail();
        } catch (ValidatorNotFoundException $e) {
            $this->assertEquals('testOverTen', $e->getName());
        }
        
        $factory->register(__NAMESPACE__); // register

        $factory->testOverTen();
    }
    
    public function testOverrideValidator()
    {
        $factory = new ValidatorFactory();

        $this->assertInstanceOf(Rules\MinValidator::class, $factory->min(5));
        
        $factory->register(__NAMESPACE__);

        $this->assertInstanceOf(MinValidator::class, $factory->min(5));
    }
}

class MinValidator implements ValidatorInterface
{
    public function assert($item)
    {
    }

    public function validate($item)
    {
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
