<?php
namespace Wandu\Validator;

use PHPUnit\Framework\TestCase;
use Wandu\Validator\Sample\SampleOverTenTester;

class TesterLoaderTest extends TestCase
{
    /** @var \Wandu\Validator\TesterLoader */
    protected $tester;
    
    public function setUp()
    {
        $this->tester = new TesterLoader();
    }
    
    public function testCaching()
    {
        $factory = new TesterLoader();

        // singleton
        static::assertSame($factory->load("integer"), $factory->load("integer"));
        static::assertSame($factory->load("string"), $factory->load("string"));
        static::assertSame($factory->load("min:5"), $factory->load("min:5"));

        static::assertNotSame($factory->load("min:7"), $factory->load("min:5"));
        static::assertSame($factory->load("min:7"), $factory->load("min:7"));
    }
    
    public function testParseWithTypo()
    {
        static::assertEquals($this->tester->load("string"), $this->tester->load("string:"));
        static::assertNotSame($this->tester->load("string"), $this->tester->load("string:"));

        static::assertEquals($this->tester->load("min:5"), $this->tester->load('min:5,,,'));
        static::assertNotSame($this->tester->load("min:5"), $this->tester->load('min:5,,,'));
        
        static::assertEquals($this->tester->load("min:5"), $this->tester->load('min:   5, , ,  '));
        static::assertNotSame($this->tester->load("min:5"), $this->tester->load('min:   5, , ,  '));
        
        static::assertEquals($this->tester->load("min:5"), $this->tester->load('min:,,,5,,,'));
        static::assertNotSame($this->tester->load("min:5"), $this->tester->load('min:,,,5,,,'));
    }

    public function testRegisterCustomTest()
    {
        $tester = new SampleOverTenTester();

        static::assertTrue($tester->test(11));
        static::assertFalse($tester->test(10));

        $this->tester = new TesterLoader([
            "over_ten" => SampleOverTenTester::class, // register
        ]);

        static::assertTrue($this->tester->create("over_ten")->test(11));
        static::assertFalse($this->tester->create("over_ten")->test(10));
    }

    public function testOverrideCustomTest()
    {
        static::assertTrue($this->tester->create("always_true")->test(11));
        static::assertTrue($this->tester->create("always_true")->test(10));

        $this->tester = new TesterLoader([
            "always_true" => SampleOverTenTester::class, // register
        ]);

        static::assertTrue($this->tester->create("always_true")->test(11));
        static::assertFalse($this->tester->create("always_true")->test(10));
    }
}
