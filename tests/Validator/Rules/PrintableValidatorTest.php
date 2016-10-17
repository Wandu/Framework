<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class PrintableValidatorTest extends ValidatorTestCase
{
    public function testScalars()
    {
        validator()->printable()->assert('30');
        validator()->printable()->assert(30);
        validator()->printable()->assert(40.5);
        validator()->printable()->assert('string');
        validator()->printable()->assert('string');

        validator()->printable()->assert(new TestPrintableValidator());

        $this->assertInvalidValueException(function () {
            validator()->printable()->assert([]);
        }, [
            'printable',
        ]);
        $this->assertInvalidValueException(function () {
            validator()->printable()->assert(new \stdClass());
        }, [
            'printable',
        ]);
    }
}

class TestPrintableValidator
{
    public function __toString()
    {
        return "Hi";
    }
}
