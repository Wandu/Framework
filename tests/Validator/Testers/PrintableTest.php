<?php
namespace Wandu\Validator\Testers;

use PHPUnit\Framework\TestCase;
use Wandu\Validator\TesterLoader;

class PrintableTest extends TestCase 
{
    /** @var \Wandu\Validator\TesterLoader */
    protected $tester;

    public function setUp()
    {
        $this->tester = new TesterLoader();
    }

    public function testScalars()
    {
        static::assertTrue($this->tester->create("printable")->test('30'));
        static::assertTrue($this->tester->create("printable")->test(30));
        static::assertTrue($this->tester->create("printable")->test(40.5));
        static::assertTrue($this->tester->create("printable")->test('string'));
        static::assertTrue($this->tester->create("printable")->test('string'));
        static::assertTrue($this->tester->create("printable")->test(new TestPrintableValidator()));

        static::assertFalse($this->tester->create("printable")->test([]));
        static::assertFalse($this->tester->create("printable")->test(new \stdClass()));
    }
}

class TestPrintableValidator
{
    public function __toString()
    {
        return "Hi";
    }
}
