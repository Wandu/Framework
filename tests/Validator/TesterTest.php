<?php
namespace Wandu\Validator;

use PHPUnit\Framework\TestCase;
use Wandu\Validator\Contracts\TesterInterface;

class TesterTest extends TestCase
{
    public function testFactory()
    {
        $factory = new TesterFactory();

        // singleton
        static::assertSame($factory->from("integer"), $factory->from("integer"));
        static::assertSame($factory->from("string"), $factory->from("string"));
        static::assertSame($factory->from("min:5"), $factory->from("min:5"));
        
        static::assertNotSame($factory->from("min:7"), $factory->from("min:5"));
    }
    
    public function testFactoryViaHelper()
    {
        static::assertSame(tester("integer"), tester("integer"));
        static::assertSame(tester("string"), tester("string"));
        static::assertSame(tester("min:5"), tester("min:5"));
        static::assertNotSame(tester("min:5"), tester("min:7"));
    }

    public function testFactoryWithTypo()
    {
        static::assertEquals(tester("string"), tester("string:"));
        static::assertNotSame(tester("string"), tester("string:"));

        static::assertEquals(tester("min:5"), tester('min:5,,,'));
        static::assertNotSame(tester("min:5"), tester('min:5,,,'));
        
        static::assertEquals(tester("min:5"), tester('min:   5, , ,  '));
        static::assertNotSame(tester("min:5"), tester('min:   5, , ,  '));
        
        static::assertEquals(tester("min:5"), tester('min:,,,5,,,'));
        static::assertNotSame(tester("min:5"), tester('min:,,,5,,,'));
    }

    public function testCustomTester()
    {
        $validator = new TesterTestOverTenTester();

        static::assertTrue($validator->test(11));
        static::assertFalse($validator->test(10));
    }

    public function testRegisterCustomTester()
    {
        $factory = new TesterFactory([
            "over_ten" => TesterTestOverTenTester::class, // register
        ]);
        $factory->setAsGlobal();

        static::assertTrue(tester("over_ten")->test(11));
        static::assertFalse(tester("over_ten")->test(10));
    }

    public function testOverrideCustomTester()
    {
        static::assertTrue(tester("always_true")->test(11));
        static::assertTrue(tester("always_true")->test(10));

        $factory = new TesterFactory([
            "always_true" => TesterTestOverTenTester::class, // register
        ]);
        $factory->setAsGlobal();

        static::assertTrue(tester("always_true")->test(11));
        static::assertFalse(tester("always_true")->test(10));
    }
}

class TesterTestOverTenTester implements TesterInterface 
{
    /**
     * {@inheritdoc}
     */
    public function test($item): bool
    {
        return $item > 10;
    }
}
