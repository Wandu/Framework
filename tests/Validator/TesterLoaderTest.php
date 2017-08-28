<?php
namespace Wandu\Validator;

use PHPUnit\Framework\TestCase;
use Wandu\Validator\Contracts\Tester;

class TesterLoaderTest extends TestCase
{
    /** @var \Wandu\Validator\TesterLoader */
    protected $tester;
    
    public function setUp()
    {
        $this->tester = new TesterLoader();
    }
    
    public function testBoolean()
    {
        static::assertTrue($this->tester->create('boolean')->test(true));
        static::assertTrue($this->tester->create('boolean')->test(false));

        static::assertFalse($this->tester->create('boolean')->test(null));
        static::assertFalse($this->tester->create('boolean')->test(30));
        static::assertFalse($this->tester->create('boolean')->test(30.0));
        static::assertFalse($this->tester->create('boolean')->test("30"));
    }

    public function testInteger()
    {
        static::assertTrue($this->tester->create('int')->test(30));

        static::assertFalse($this->tester->create('int')->test(null));
        static::assertFalse($this->tester->create('int')->test("30"));
    }

    public function testNumeric()
    {
        static::assertTrue($this->tester->create("numeric")->test(30));
        static::assertTrue($this->tester->create("numeric")->test("30"));
        static::assertTrue($this->tester->create("numeric")->test(0347123));
        static::assertTrue($this->tester->create("numeric")->test("0347123"));
        static::assertTrue($this->tester->create("numeric")->test(0xfffff));

        if (!version_compare(PHP_VERSION, '7', '>=')) {
            // php7 not support hex string.
            // https://wiki.php.net/rfc/remove_hex_support_in_numeric_strings
            static::assertTrue($this->tester->create("numeric")->test('0xfffff'));
        }

        static::assertTrue($this->tester->create("numeric")->test(30.33));
        static::assertTrue($this->tester->create("numeric")->test('30.33'));

        static::assertFalse($this->tester->create("numeric")->test(null));
        static::assertFalse($this->tester->create("numeric")->test("string"));
    }

    public function testIntegerable()
    {
        static::assertTrue($this->tester->create('integerable')->test('30'));
        static::assertTrue($this->tester->create('integerable')->test(30));
        static::assertTrue($this->tester->create('integerable')->test('-30'));
        static::assertTrue($this->tester->create('integerable')->test(-30));
        static::assertTrue($this->tester->create('integerable')->test(0));
        static::assertTrue($this->tester->create('integerable')->test('0'));

        static::assertFalse($this->tester->create('integerable')->test(null));
        static::assertFalse($this->tester->create('integerable')->test(40.5));
        static::assertFalse($this->tester->create('integerable')->test('40.5'));
        static::assertFalse($this->tester->create('integerable')->test(40.0));
        static::assertFalse($this->tester->create('integerable')->test('40.0'));
        static::assertFalse($this->tester->create('integerable')->test(-40.0));
        static::assertFalse($this->tester->create('integerable')->test('-40.0'));
        static::assertFalse($this->tester->create('integerable')->test(0.0));
        static::assertFalse($this->tester->create('integerable')->test('0.0'));
        static::assertFalse($this->tester->create('integerable')->test('string'));
        static::assertFalse($this->tester->create('integerable')->test([]));
        static::assertFalse($this->tester->create('integerable')->test(new \stdClass()));
    }

    public function testFloat()
    {
        static::assertTrue($this->tester->create('float')->test(30.1));
        static::assertTrue($this->tester->create('float')->test(30.0));

        static::assertFalse($this->tester->create('float')->test(null));
        static::assertFalse($this->tester->create('float')->test("30"));
        static::assertFalse($this->tester->create('float')->test(30));
    }

    public function testFloatable()
    {
        static::assertTrue($this->tester->create('floatable')->test('30'));
        static::assertTrue($this->tester->create('floatable')->test(30));
        static::assertTrue($this->tester->create('floatable')->test('-30'));
        static::assertTrue($this->tester->create('floatable')->test(-30));
        static::assertTrue($this->tester->create('floatable')->test(0));
        static::assertTrue($this->tester->create('floatable')->test('0'));

        static::assertTrue($this->tester->create('floatable')->test('30.0'));
        static::assertTrue($this->tester->create('floatable')->test(30.0));
        static::assertTrue($this->tester->create('floatable')->test('-30.0'));
        static::assertTrue($this->tester->create('floatable')->test(-30.0));
        static::assertTrue($this->tester->create('floatable')->test('30.5'));
        static::assertTrue($this->tester->create('floatable')->test(30.5));
        static::assertTrue($this->tester->create('floatable')->test('-30.5'));
        static::assertTrue($this->tester->create('floatable')->test(-30.5));
        static::assertTrue($this->tester->create('floatable')->test(0.0));
        static::assertTrue($this->tester->create('floatable')->test('0.0'));

        static::assertFalse($this->tester->create('floatable')->test(null));
        static::assertFalse($this->tester->create('floatable')->test('string'));
        static::assertFalse($this->tester->create('floatable')->test([]));
        static::assertFalse($this->tester->create('floatable')->test(new \stdClass()));
    }

    public function testString()
    {
        static::assertTrue($this->tester->create('string')->test('30'));

        static::assertFalse($this->tester->create('string')->test(null));
        static::assertFalse($this->tester->create('string')->test(30));
    }

    public function testStringable()
    {
        static::assertTrue($this->tester->create('stringable')->test('30'));
        static::assertTrue($this->tester->create('stringable')->test(30));
        static::assertTrue($this->tester->create('stringable')->test(40.5));
        static::assertTrue($this->tester->create('stringable')->test('string'));
        static::assertTrue($this->tester->create('stringable')->test('string'));

        static::assertFalse($this->tester->create('stringable')->test(null));
        static::assertFalse($this->tester->create('stringable')->test([]));
        static::assertFalse($this->tester->create('stringable')->test(new \stdClass()));
    }

    public function testMin()
    {
        static::assertTrue($this->tester->load('min:5')->test(100));
        static::assertTrue($this->tester->load('min:5')->test(6));
        static::assertTrue($this->tester->load('min:5')->test(5));

        static::assertTrue($this->tester->load('min:5')->test('100'));
        static::assertTrue($this->tester->load('min:5')->test('6'));
        static::assertTrue($this->tester->load('min:5')->test('5'));

        static::assertFalse($this->tester->load('min:5')->test(null));
        static::assertFalse($this->tester->load('min:5')->test(4));
        static::assertFalse($this->tester->load('min:5')->test('4'));
    }

    public function testMax()
    {
        static::assertTrue($this->tester->load('max:5')->test(0));
        static::assertTrue($this->tester->load('max:5')->test(4));
        static::assertTrue($this->tester->load('max:5')->test(5));

        static::assertTrue($this->tester->load('max:5')->test('0'));
        static::assertTrue($this->tester->load('max:5')->test('4'));
        static::assertTrue($this->tester->load('max:5')->test('5'));

        static::assertFalse($this->tester->load('max:5')->test(null));
        static::assertFalse($this->tester->load('max:5')->test(6));
        static::assertFalse($this->tester->load('max:5')->test('6'));
    }

    public function testLengthMin()
    {
        static::assertTrue($this->tester->load('length_min:5')->test('aaaaaaa'));
        static::assertTrue($this->tester->load('length_min:5')->test('aaaaaa'));
        static::assertTrue($this->tester->load('length_min:5')->test('aaaaa'));

        static::assertTrue($this->tester->load('length_min:5')->test(1111111));
        static::assertTrue($this->tester->load('length_min:5')->test(111111));
        static::assertTrue($this->tester->load('length_min:5')->test(11111));

        static::assertTrue($this->tester->load('length_min:5')->test([1, 2, 3, 4, 5, 6, 7, ]));
        static::assertTrue($this->tester->load('length_min:5')->test([1, 2, 3, 4, 5, 6, ]));
        static::assertTrue($this->tester->load('length_min:5')->test([1, 2, 3, 4, 5, ]));

        static::assertFalse($this->tester->load('length_min:5')->test(null));
        static::assertFalse($this->tester->load('length_min:5')->test('aaaa'));
        static::assertFalse($this->tester->load('length_min:5')->test(1111));
        static::assertFalse($this->tester->load('length_min:5')->test([1, 2, 3, 4, ]));
    }

    public function testLengthMax()
    {
        static::assertTrue($this->tester->load('length_max:5')->test(''));
        static::assertTrue($this->tester->load('length_max:5')->test('aaaa'));
        static::assertTrue($this->tester->load('length_max:5')->test('aaaaa'));

        static::assertTrue($this->tester->load('length_max:5')->test(1));
        static::assertTrue($this->tester->load('length_max:5')->test(1111));
        static::assertTrue($this->tester->load('length_max:5')->test(11111));

        static::assertTrue($this->tester->load('length_max:5')->test([1, ]));
        static::assertTrue($this->tester->load('length_max:5')->test([1, 2, 3, 4, ]));
        static::assertTrue($this->tester->load('length_max:5')->test([1, 2, 3, 4, 5, ]));

        static::assertFalse($this->tester->load('length_max:5')->test(null));
        static::assertFalse($this->tester->load('length_max:5')->test('aaaaaa'));
        static::assertFalse($this->tester->load('length_max:5')->test(111111));
        static::assertFalse($this->tester->load('length_max:5')->test([1, 2, 3, 4, 5, 6, ]));
    }

    public function testAfter()
    {
        static::assertTrue($this->tester->load('after:now')->test(time() + 1));
        static::assertFalse($this->tester->load('after:now')->test(time() - 1));

        static::assertTrue($this->tester->load('after:2017-05-30')->test(1496102401));
        static::assertFalse($this->tester->load('after:2017-05-30')->test(1496102399));
    }

    public function testBefore()
    {
        static::assertTrue($this->tester->load('before:now')->test(time() - 1));
        static::assertFalse($this->tester->load('before:now')->test(time() + 1));

        static::assertTrue($this->tester->load('before:2017-05-30')->test(1496102399));
        static::assertFalse($this->tester->load('before:2017-05-30')->test(1496102401));
    }

    public function testGreaterThan()
    {
        static::assertTrue($this->tester->load('greater_than:finish')->test(200, ["finish" => 199]));
        static::assertFalse($this->tester->load('greater_than:finish')->test(200, ["finish" => 200]));
        static::assertFalse($this->tester->load('greater_than:finish')->test(200, ["finish" => 201]));
        
        // unknown
        static::assertFalse($this->tester->load('greater_than:finish')->test(null));
        static::assertFalse($this->tester->load('greater_than:finish')->test(100));

        // same gt
        static::assertTrue($this->tester->load('gt:finish')->test(200, ["finish" => 199]));
        static::assertFalse($this->tester->load('gt:finish')->test(200, ["finish" => 200]));
        static::assertFalse($this->tester->load('gt:finish')->test(200, ["finish" => 201]));

        // unknown
        static::assertFalse($this->tester->load('gt:finish')->test(null));
        static::assertFalse($this->tester->load('gt:finish')->test(100));
    }

    public function testGreaterThanOrEqual()
    {
        static::assertTrue($this->tester->load('greater_than_or_equal:finish')->test(200, ["finish" => 199]));
        static::assertTrue($this->tester->load('greater_than_or_equal:finish')->test(200, ["finish" => 200]));
        static::assertFalse($this->tester->load('greater_than_or_equal:finish')->test(200, ["finish" => 201]));

        // unknown
        static::assertFalse($this->tester->load('greater_than_or_equal:finish')->test(null));
        static::assertFalse($this->tester->load('greater_than_or_equal:finish')->test(100));

        // same gt
        static::assertTrue($this->tester->load('gte:finish')->test(200, ["finish" => 199]));
        static::assertTrue($this->tester->load('gte:finish')->test(200, ["finish" => 200]));
        static::assertFalse($this->tester->load('gte:finish')->test(200, ["finish" => 201]));

        // unknown
        static::assertFalse($this->tester->load('gte:finish')->test(null));
        static::assertFalse($this->tester->load('gte:finish')->test(100));
    }

    public function testLessThan()
    {
        static::assertTrue($this->tester->load('less_than:finish')->test(200, ["finish" => 201]));
        static::assertFalse($this->tester->load('less_than:finish')->test(200, ["finish" => 200]));
        static::assertFalse($this->tester->load('less_than:finish')->test(200, ["finish" => 199]));

        // unknown
        static::assertFalse($this->tester->load('less_than:finish')->test(null));
        static::assertFalse($this->tester->load('less_than:finish')->test(100));

        // same gt
        static::assertTrue($this->tester->load('lt:finish')->test(200, ["finish" => 201]));
        static::assertFalse($this->tester->load('lt:finish')->test(200, ["finish" => 200]));
        static::assertFalse($this->tester->load('lt:finish')->test(200, ["finish" => 199]));

        // unknown
        static::assertFalse($this->tester->load('lt:finish')->test(null));
        static::assertFalse($this->tester->load('lt:finish')->test(100));
    }

    public function testLessOrEqualThan()
    {
        static::assertTrue($this->tester->load('less_than_or_equal:finish')->test(200, ["finish" => 201]));
        static::assertTrue($this->tester->load('less_than_or_equal:finish')->test(200, ["finish" => 200]));
        static::assertFalse($this->tester->load('less_than_or_equal:finish')->test(200, ["finish" => 199]));

        // unknown
        static::assertFalse($this->tester->load('less_than_or_equal:finish')->test(null));
        static::assertFalse($this->tester->load('less_than_or_equal:finish')->test(100));

        // same gt
        static::assertTrue($this->tester->load('lte:finish')->test(200, ["finish" => 201]));
        static::assertTrue($this->tester->load('lte:finish')->test(200, ["finish" => 200]));
        static::assertFalse($this->tester->load('lte:finish')->test(200, ["finish" => 199]));

        // unknown
        static::assertFalse($this->tester->load('lte:finish')->test(null));
        static::assertFalse($this->tester->load('lte:finish')->test(100));
    }

    public function testEqualTo()
    {
        static::assertFalse($this->tester->load('equal_to:finish')->test(200, ["finish" => 201]));
        static::assertTrue($this->tester->load('equal_to:finish')->test(200, ["finish" => 200]));
        static::assertFalse($this->tester->load('equal_to:finish')->test(200, ["finish" => 199]));

        // unknown
        static::assertFalse($this->tester->load('equal_to:finish')->test(null));
        static::assertFalse($this->tester->load('equal_to:finish')->test(100));

        // same gt
        static::assertFalse($this->tester->load('eq:finish')->test(200, ["finish" => 201]));
        static::assertTrue($this->tester->load('eq:finish')->test(200, ["finish" => 200]));
        static::assertFalse($this->tester->load('eq:finish')->test(200, ["finish" => 199]));

        // unknown
        static::assertFalse($this->tester->load('eq:finish')->test(null));
        static::assertFalse($this->tester->load('eq:finish')->test(100));
    }
    
    public function testFactory()
    {
        $factory = new TesterLoader();

        // singleton
        static::assertSame($factory->load("integer"), $factory->load("integer"));
        static::assertSame($factory->load("string"), $factory->load("string"));
        static::assertSame($factory->load("min:5"), $factory->load("min:5"));
        
        static::assertNotSame($factory->load("min:7"), $factory->load("min:5"));
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

    public function testCustomTester()
    {
        $tester = new TesterTestOverTenTester();

        static::assertTrue($tester->test(11));
        static::assertFalse($tester->test(10));
    }

    public function testRegisterCustomTest()
    {
        $this->tester = new TesterLoader([
            "over_ten" => TesterTestOverTenTester::class, // register
        ]); 

        static::assertTrue($this->tester->create("over_ten")->test(11));
        static::assertFalse($this->tester->create("over_ten")->test(10));
    }

    public function testOverrideCustomTest()
    {
        static::assertTrue($this->tester->create("always_true")->test(11));
        static::assertTrue($this->tester->create("always_true")->test(10));

        $this->tester = new TesterLoader([
            "always_true" => TesterTestOverTenTester::class, // register
        ]);

        static::assertTrue($this->tester->create("always_true")->test(11));
        static::assertFalse($this->tester->create("always_true")->test(10));
    }
}

class TesterTestOverTenTester implements Tester 
{
    /**
     * {@inheritdoc}
     */
    public function test($data, $origin = null, array $keys = []): bool
    {
        return $data > 10;
    }
}
