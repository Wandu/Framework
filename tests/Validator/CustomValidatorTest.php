<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\ValidatorNotFoundException;
use Wandu\Validator\Rules\ValidatorAbstract;

class CustomValidatorTest extends ValidatorTestCase
{
    public function tearDown()
    {
        ValidatorFactory::clearGlobal();
    }
    
    public function testValidate()
    {
        $validator = new TestOverTenValidator();

        $validator->assert(11);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(10);
        }, [
            'test_over_ten:hello',
        ]);
    }
    
    public function testValidateWithOthers()
    {
        $validator = validator()->pipeline([
            validator()->max(20),
            new TestOverTenValidator(),
        ]);

        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(10);
        }, [
            'test_over_ten:hello',
        ]);
        $this->assertInvalidValueException(function () use ($validator) {
            $validator->assert(21);
        }, [
            'max:20',
        ]);
    }

    public function testRegisterBaseNamespace()
    {
        ValidatorFactory::clearGlobal();
        (new ValidatorFactory)->setAsGlobal();
        try {
            validator()->testOverTen();
            static::fail();
        } catch (ValidatorNotFoundException $e) {
            static::assertEquals('testOverTen', $e->getName());
        }
        
        validator()->register(__NAMESPACE__); // register

        validator()->testOverTen()->assert(11);
        validator()->from('test_over_ten')->assert(11);
        validator()->from([
            'age' => 'required|test_over_ten'
        ])->assert([
            'age' => 11,
        ]);

        $this->assertInvalidValueException(function () {
            validator()->testOverTen()->assert(10);
        }, ['test_over_ten:hello']);
        $this->assertInvalidValueException(function () {
            validator()->from('test_over_ten')->assert(10);
        }, ['test_over_ten:hello']);
        $this->assertInvalidValueException(function () {
            validator()->from([
                'age' => 'required|test_over_ten'
            ])->assert([
                'age' => 10,
            ]);
        }, ['test_over_ten:hello@age']);
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
    const ERROR_TYPE = 'test_over_ten:{{something}}';
    
    protected $something = "hello";
    
    public function test($item)
    {
        return $item > 10;
    }
}
