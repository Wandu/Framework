<?php
namespace Wandu\Validator\Rules;

use PHPUnit_Framework_TestCase;
use Wandu\Validator\Exception\InvalidValueException;
use function Wandu\Validator\validator;

class OptionalValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testOptional()
    {
        $validator = validator()->optional();

        $this->assertTrue($validator->validate(null));
        $this->assertTrue($validator->validate(''));

        $this->assertFalse($validator->validate('1'));
        $this->assertFalse($validator->validate(false));
        $this->assertFalse($validator->validate(0));
    }

    public function testOptionalWithOthers()
    {
        $validator = validator()->optional(validator()->integer());

        $this->assertTrue($validator->validate(null));
        $this->assertTrue($validator->validate(''));

        $this->assertTrue($validator->validate(0)); // true
        $this->assertTrue($validator->validate(111)); // true

        $this->assertFalse($validator->validate('1'));
        $this->assertFalse($validator->validate(false));
    }

    public function testOptionalWithChaining()
    {
        $validator = validator()->optional()->integer();

        $this->assertTrue($validator->validate(null));
        $this->assertTrue($validator->validate(''));

        $this->assertTrue($validator->validate(0)); // true
        $this->assertTrue($validator->validate(111)); // true

        $this->assertFalse($validator->validate('1'));
        $this->assertFalse($validator->validate(false));
    }

    public function testOptionalAssert()
    {
        $validator = validator()->optional()->integer();

        $validator->assert(null);
        $validator->assert(10);

        try {
            $validator->assert('30');
        } catch (InvalidValueException $e) {
            $this->assertEquals('optional', $e->getType());

            $innerExceptions = $e->getExceptions();
            $this->assertEquals(1, count($innerExceptions));
            $this->assertEquals('type.integer', $innerExceptions[0]->getType());
        }

        try {
            $validator->assert('30', true);
        } catch (InvalidValueException $e) {
            $this->assertEquals('optional', $e->getType());
            $this->assertEquals(0, count($e->getExceptions()));
        }
    }
}
