<?php
namespace Wandu\Validator;

use PHPUnit_Framework_TestCase;
use Wandu\Validator\Exception\InvalidValueException;
use Wandu\Validator\Rules\ValidatorAbstract;

class CustomValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $validator = new TestCustomValidator();

        $this->assertTrue($validator->validate(111));
        $this->assertFalse($validator->validate(112));
        
        $validator->assert(111);
        
        try {
            $validator->assert(112);
        } catch (InvalidValueException $e) {
            $this->assertEquals('test.custom', $e->getType());
            $this->assertEquals('it must be 111', $e->getMessage());
        }
    }
}

class TestCustomValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'test.custom';
    const ERROR_MESSAGE = 'it must be 111';
    
    public function validate($item)
    {
        return $item === 111;
    }
}
