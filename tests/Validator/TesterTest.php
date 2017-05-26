<?php
namespace Wandu\Validator;

use PHPUnit\Framework\TestCase;
use Wandu\Validator\Contracts\TesterInterface;

class TesterTest extends TestCase
{
    public function testBoolean()
    {
        static::assertTrue(tester('boolean')->test(true));
        static::assertTrue(tester('boolean')->test(false));

        static::assertFalse(tester('boolean')->test(null));
        static::assertFalse(tester('boolean')->test(30));
        static::assertFalse(tester('boolean')->test(30.0));
        static::assertFalse(tester('boolean')->test("30"));
    }

    public function testInteger()
    {
        static::assertTrue(tester('int')->test(30));

        static::assertFalse(tester('int')->test(null));
        static::assertFalse(tester('int')->test("30"));
    }

    public function testNumeric()
    {
        static::assertTrue(tester("numeric")->test(30));
        static::assertTrue(tester("numeric")->test("30"));
        static::assertTrue(tester("numeric")->test(0347123));
        static::assertTrue(tester("numeric")->test("0347123"));
        static::assertTrue(tester("numeric")->test(0xfffff));

        if (!version_compare(PHP_VERSION, '7', '>=')) {
            // php7 not support hex string.
            // https://wiki.php.net/rfc/remove_hex_support_in_numeric_strings
            static::assertTrue(tester("numeric")->test('0xfffff'));
        }

        static::assertTrue(tester("numeric")->test(30.33));
        static::assertTrue(tester("numeric")->test('30.33'));

        static::assertFalse(tester("numeric")->test(null));
        static::assertFalse(tester("numeric")->test("string"));
    }

    public function testIntegerable()
    {
        static::assertTrue(tester('integerable')->test('30'));
        static::assertTrue(tester('integerable')->test(30));
        static::assertTrue(tester('integerable')->test('-30'));
        static::assertTrue(tester('integerable')->test(-30));
        static::assertTrue(tester('integerable')->test(0));
        static::assertTrue(tester('integerable')->test('0'));

        static::assertFalse(tester('integerable')->test(null));
        static::assertFalse(tester('integerable')->test(40.5));
        static::assertFalse(tester('integerable')->test('40.5'));
        static::assertFalse(tester('integerable')->test(40.0));
        static::assertFalse(tester('integerable')->test('40.0'));
        static::assertFalse(tester('integerable')->test(-40.0));
        static::assertFalse(tester('integerable')->test('-40.0'));
        static::assertFalse(tester('integerable')->test(0.0));
        static::assertFalse(tester('integerable')->test('0.0'));
        static::assertFalse(tester('integerable')->test('string'));
        static::assertFalse(tester('integerable')->test([]));
        static::assertFalse(tester('integerable')->test(new \stdClass()));
    }

    public function testFloat()
    {
        static::assertTrue(tester('float')->test(30.1));
        static::assertTrue(tester('float')->test(30.0));

        static::assertFalse(tester('float')->test(null));
        static::assertFalse(tester('float')->test("30"));
        static::assertFalse(tester('float')->test(30));
    }

    public function testFloatable()
    {
        static::assertTrue(tester('floatable')->test('30'));
        static::assertTrue(tester('floatable')->test(30));
        static::assertTrue(tester('floatable')->test('-30'));
        static::assertTrue(tester('floatable')->test(-30));
        static::assertTrue(tester('floatable')->test(0));
        static::assertTrue(tester('floatable')->test('0'));

        static::assertTrue(tester('floatable')->test('30.0'));
        static::assertTrue(tester('floatable')->test(30.0));
        static::assertTrue(tester('floatable')->test('-30.0'));
        static::assertTrue(tester('floatable')->test(-30.0));
        static::assertTrue(tester('floatable')->test('30.5'));
        static::assertTrue(tester('floatable')->test(30.5));
        static::assertTrue(tester('floatable')->test('-30.5'));
        static::assertTrue(tester('floatable')->test(-30.5));
        static::assertTrue(tester('floatable')->test(0.0));
        static::assertTrue(tester('floatable')->test('0.0'));

        static::assertFalse(tester('floatable')->test(null));
        static::assertFalse(tester('floatable')->test('string'));
        static::assertFalse(tester('floatable')->test([]));
        static::assertFalse(tester('floatable')->test(new \stdClass()));
    }

    public function testString()
    {
        static::assertTrue(tester('string')->test('30'));

        static::assertFalse(tester('string')->test(null));
        static::assertFalse(tester('string')->test(30));
    }

    public function testStringable()
    {
        static::assertTrue(tester('stringable')->test('30'));
        static::assertTrue(tester('stringable')->test(30));
        static::assertTrue(tester('stringable')->test(40.5));
        static::assertTrue(tester('stringable')->test('string'));
        static::assertTrue(tester('stringable')->test('string'));

        static::assertFalse(tester('stringable')->test(null));
        static::assertFalse(tester('stringable')->test([]));
        static::assertFalse(tester('stringable')->test(new \stdClass()));
    }

    public function testMin()
    {
        static::assertTrue(tester('min:5')->test(100));
        static::assertTrue(tester('min:5')->test(6));
        static::assertTrue(tester('min:5')->test(5));

        static::assertTrue(tester('min:5')->test('100'));
        static::assertTrue(tester('min:5')->test('6'));
        static::assertTrue(tester('min:5')->test('5'));

        static::assertFalse(tester('min:5')->test(null));
        static::assertFalse(tester('min:5')->test(4));
        static::assertFalse(tester('min:5')->test('4'));
    }

    public function testMax()
    {
        static::assertTrue(tester('max:5')->test(0));
        static::assertTrue(tester('max:5')->test(4));
        static::assertTrue(tester('max:5')->test(5));

        static::assertTrue(tester('max:5')->test('0'));
        static::assertTrue(tester('max:5')->test('4'));
        static::assertTrue(tester('max:5')->test('5'));

        static::assertFalse(tester('max:5')->test(null));
        static::assertFalse(tester('max:5')->test(6));
        static::assertFalse(tester('max:5')->test('6'));
    }

    public function testLengthMin()
    {
        static::assertTrue(tester('length_min:5')->test('aaaaaaa'));
        static::assertTrue(tester('length_min:5')->test('aaaaaa'));
        static::assertTrue(tester('length_min:5')->test('aaaaa'));

        static::assertTrue(tester('length_min:5')->test(1111111));
        static::assertTrue(tester('length_min:5')->test(111111));
        static::assertTrue(tester('length_min:5')->test(11111));

        static::assertTrue(tester('length_min:5')->test([1, 2, 3, 4, 5, 6, 7, ]));
        static::assertTrue(tester('length_min:5')->test([1, 2, 3, 4, 5, 6, ]));
        static::assertTrue(tester('length_min:5')->test([1, 2, 3, 4, 5, ]));

        static::assertFalse(tester('length_min:5')->test(null));
        static::assertFalse(tester('length_min:5')->test('aaaa'));
        static::assertFalse(tester('length_min:5')->test(1111));
        static::assertFalse(tester('length_min:5')->test([1, 2, 3, 4, ]));
    }

    public function testLengthMax()
    {
        static::assertTrue(tester('length_max:5')->test(''));
        static::assertTrue(tester('length_max:5')->test('aaaa'));
        static::assertTrue(tester('length_max:5')->test('aaaaa'));

        static::assertTrue(tester('length_max:5')->test(1));
        static::assertTrue(tester('length_max:5')->test(1111));
        static::assertTrue(tester('length_max:5')->test(11111));

        static::assertTrue(tester('length_max:5')->test([1, ]));
        static::assertTrue(tester('length_max:5')->test([1, 2, 3, 4, ]));
        static::assertTrue(tester('length_max:5')->test([1, 2, 3, 4, 5, ]));

        static::assertFalse(tester('length_max:5')->test(null));
        static::assertFalse(tester('length_max:5')->test('aaaaaa'));
        static::assertFalse(tester('length_max:5')->test(111111));
        static::assertFalse(tester('length_max:5')->test([1, 2, 3, 4, 5, 6, ]));
    }
    
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
