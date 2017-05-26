<?php
namespace Wandu\Validator\Rules;

use PHPUnit\Framework\TestCase;
use stdClass;
use function Wandu\Validator\tester;

class RegExpValidatorTest extends TestCase 
{
    public function testRegExp()
    {
        $tester = tester("regexp", "/^hello_world$/");

        static::assertTrue($tester->test("hello_world"));

        static::assertFalse($tester->test("other string"));
        static::assertFalse($tester->test(new stdClass));
    }

    public function testRegExpFrom()
    {
        $tester = tester("regexp:/^hello_world$/");

        static::assertTrue($tester->test("hello_world"));

        static::assertFalse($tester->test("other string"));
        static::assertFalse($tester->test(new stdClass));
    }

    public function testRegExpHasComma()
    {
        $tester = tester("regexp:/^\\d{3,5}$/");

        static::assertTrue($tester->test("100"));
        static::assertTrue($tester->test("1000"));
        static::assertTrue($tester->test("10000"));

        static::assertFalse($tester->test("10"));
        static::assertFalse($tester->test('100000'));
    }
}
