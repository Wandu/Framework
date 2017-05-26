<?php
namespace Wandu\Validator;

use PHPUnit\Framework\TestCase;

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
}
