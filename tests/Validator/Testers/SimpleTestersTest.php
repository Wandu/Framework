<?php
namespace Wandu\Validator\Testers;

use PHPUnit\Framework\TestCase;
use Wandu\Validator\TesterLoader;

class SimpleTestersTest extends TestCase
{
    /** @var \Wandu\Validator\TesterLoader */
    protected $tester;

    public function setUp()
    {
        $this->tester = new TesterLoader();
    }

    public function testBoolean()
    {
        static::assertTrue($this->tester->load('boolean')->test(true));
        static::assertTrue($this->tester->load('boolean')->test(false));

        static::assertFalse($this->tester->load('boolean')->test(null));
        static::assertFalse($this->tester->load('boolean')->test(30));
        static::assertFalse($this->tester->load('boolean')->test(30.0));
        static::assertFalse($this->tester->load('boolean')->test("30"));
    }

    public function testBetween()
    {
        static::assertFalse($this->tester->load('between:1,3')->test(0));
        static::assertTrue($this->tester->load('between:1,3')->test(1));
        static::assertTrue($this->tester->load('between:1,3')->test(3));
        static::assertFalse($this->tester->load('between:1,3')->test(4));

        static::assertFalse($this->tester->load('between:1,3')->test("0"));
        static::assertTrue($this->tester->load('between:1,3')->test("1"));
        static::assertTrue($this->tester->load('between:1,3')->test("11"));
        static::assertTrue($this->tester->load('between:1,3')->test("3"));
        static::assertFalse($this->tester->load('between:1,3')->test("33"));
    }

    public function testLengthBetween()
    {
        static::assertFalse($this->tester->load('length_between:1,3')->test([]));
        static::assertTrue($this->tester->load('length_between:1,3')->test([1, ]));
        static::assertTrue($this->tester->load('length_between:1,3')->test([1, 1, 1, ]));
        static::assertFalse($this->tester->load('length_between:1,3')->test([1, 1, 1, 1, ]));

        static::assertFalse($this->tester->load('length_between:1,3')->test(""));
        static::assertTrue($this->tester->load('length_between:1,3')->test("1"));
        static::assertTrue($this->tester->load('length_between:1,3')->test("111"));
        static::assertFalse($this->tester->load('length_between:1,3')->test("1111"));
    }

    public function testInteger()
    {
        static::assertTrue($this->tester->load('int')->test(30));

        static::assertFalse($this->tester->load('int')->test(null));
        static::assertFalse($this->tester->load('int')->test("30"));
    }

    public function testNumeric()
    {
        static::assertTrue($this->tester->load("numeric")->test(30));
        static::assertTrue($this->tester->load("numeric")->test("30"));
        static::assertTrue($this->tester->load("numeric")->test(0347123));
        static::assertTrue($this->tester->load("numeric")->test("0347123"));
        static::assertTrue($this->tester->load("numeric")->test(0xfffff));

        if (!version_compare(PHP_VERSION, '7', '>=')) {
            // php7 not support hex string.
            // https://wiki.php.net/rfc/remove_hex_support_in_numeric_strings
            static::assertTrue($this->tester->load("numeric")->test('0xfffff'));
        }

        static::assertTrue($this->tester->load("numeric")->test(30.33));
        static::assertTrue($this->tester->load("numeric")->test('30.33'));

        static::assertFalse($this->tester->load("numeric")->test(null));
        static::assertFalse($this->tester->load("numeric")->test("string"));
    }

    public function testIntegerable()
    {
        static::assertTrue($this->tester->load('integerable')->test('30'));
        static::assertTrue($this->tester->load('integerable')->test(30));
        static::assertTrue($this->tester->load('integerable')->test('-30'));
        static::assertTrue($this->tester->load('integerable')->test(-30));
        static::assertTrue($this->tester->load('integerable')->test(0));
        static::assertTrue($this->tester->load('integerable')->test('0'));

        static::assertFalse($this->tester->load('integerable')->test(null));
        static::assertFalse($this->tester->load('integerable')->test(40.5));
        static::assertFalse($this->tester->load('integerable')->test('40.5'));
        static::assertFalse($this->tester->load('integerable')->test(40.0));
        static::assertFalse($this->tester->load('integerable')->test('40.0'));
        static::assertFalse($this->tester->load('integerable')->test(-40.0));
        static::assertFalse($this->tester->load('integerable')->test('-40.0'));
        static::assertFalse($this->tester->load('integerable')->test(0.0));
        static::assertFalse($this->tester->load('integerable')->test('0.0'));
        static::assertFalse($this->tester->load('integerable')->test('string'));
        static::assertFalse($this->tester->load('integerable')->test([]));
        static::assertFalse($this->tester->load('integerable')->test(new \stdClass()));
    }

    public function testFloat()
    {
        static::assertTrue($this->tester->load('float')->test(30.1));
        static::assertTrue($this->tester->load('float')->test(30.0));

        static::assertFalse($this->tester->load('float')->test(null));
        static::assertFalse($this->tester->load('float')->test("30"));
        static::assertFalse($this->tester->load('float')->test(30));
    }

    public function testFloatable()
    {
        static::assertTrue($this->tester->load('floatable')->test('30'));
        static::assertTrue($this->tester->load('floatable')->test(30));
        static::assertTrue($this->tester->load('floatable')->test('-30'));
        static::assertTrue($this->tester->load('floatable')->test(-30));
        static::assertTrue($this->tester->load('floatable')->test(0));
        static::assertTrue($this->tester->load('floatable')->test('0'));

        static::assertTrue($this->tester->load('floatable')->test('30.0'));
        static::assertTrue($this->tester->load('floatable')->test(30.0));
        static::assertTrue($this->tester->load('floatable')->test('-30.0'));
        static::assertTrue($this->tester->load('floatable')->test(-30.0));
        static::assertTrue($this->tester->load('floatable')->test('30.5'));
        static::assertTrue($this->tester->load('floatable')->test(30.5));
        static::assertTrue($this->tester->load('floatable')->test('-30.5'));
        static::assertTrue($this->tester->load('floatable')->test(-30.5));
        static::assertTrue($this->tester->load('floatable')->test(0.0));
        static::assertTrue($this->tester->load('floatable')->test('0.0'));

        static::assertFalse($this->tester->load('floatable')->test(null));
        static::assertFalse($this->tester->load('floatable')->test('string'));
        static::assertFalse($this->tester->load('floatable')->test([]));
        static::assertFalse($this->tester->load('floatable')->test(new \stdClass()));
    }

    public function testString()
    {
        static::assertTrue($this->tester->load('string')->test('30'));

        static::assertFalse($this->tester->load('string')->test(null));
        static::assertFalse($this->tester->load('string')->test(30));
    }

    public function testStringable()
    {
        static::assertTrue($this->tester->load('stringable')->test('30'));
        static::assertTrue($this->tester->load('stringable')->test(30));
        static::assertTrue($this->tester->load('stringable')->test(40.5));
        static::assertTrue($this->tester->load('stringable')->test('string'));
        static::assertTrue($this->tester->load('stringable')->test('string'));

        static::assertFalse($this->tester->load('stringable')->test(null));
        static::assertFalse($this->tester->load('stringable')->test([]));
        static::assertFalse($this->tester->load('stringable')->test(new \stdClass()));
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
}
