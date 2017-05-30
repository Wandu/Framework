<?php
namespace Wandu\Validator\Testers;

use PHPUnit\Framework\TestCase;
use function Wandu\Validator\tester;
use Wandu\Validator\TesterFactory;

class PrintableTest extends TestCase 
{
    /** @var \Wandu\Validator\TesterFactory */
    protected $tester;

    public function setUp()
    {
        $this->tester = new TesterFactory();
    }

    public function testScalars()
    {
        static::assertTrue($this->tester->parse("printable")->test('30'));
        static::assertTrue($this->tester->parse("printable")->test(30));
        static::assertTrue($this->tester->parse("printable")->test(40.5));
        static::assertTrue($this->tester->parse("printable")->test('string'));
        static::assertTrue($this->tester->parse("printable")->test('string'));
        static::assertTrue($this->tester->parse("printable")->test(new TestPrintableValidator()));

        static::assertFalse($this->tester->parse("printable")->test([]));
        static::assertFalse($this->tester->parse("printable")->test(new \stdClass()));
    }
}

class TestPrintableValidator
{
    public function __toString()
    {
        return "Hi";
    }
}
