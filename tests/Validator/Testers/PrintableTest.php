<?php
namespace Wandu\Validator\Testers;

use PHPUnit\Framework\TestCase;
use function Wandu\Validator\tester;

class PrintableTest extends TestCase 
{
    public function testScalars()
    {
        static::assertTrue(tester("printable")->test('30'));
        static::assertTrue(tester("printable")->test(30));
        static::assertTrue(tester("printable")->test(40.5));
        static::assertTrue(tester("printable")->test('string'));
        static::assertTrue(tester("printable")->test('string'));
        static::assertTrue(tester("printable")->test(new TestPrintableValidator()));

        static::assertFalse(tester("printable")->test([]));
        static::assertFalse(tester("printable")->test(new \stdClass()));
    }
}

class TestPrintableValidator
{
    public function __toString()
    {
        return "Hi";
    }
}
