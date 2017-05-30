<?php
namespace Wandu\Validator\Rules;

use PHPUnit\Framework\TestCase;
use stdClass;
use Wandu\Validator\TesterFactory;

class RegExpValidatorTest extends TestCase 
{
    /** @var \Wandu\Validator\TesterFactory */
    protected $tester;

    public function setUp()
    {
        $this->tester = new TesterFactory();
    }

    public function testRegExp()
    {
        $tester = $this->tester->create("regexp", ["/^hello_world$/"]);

        static::assertTrue($tester->test("hello_world"));

        static::assertFalse($tester->test("other string"));
        static::assertFalse($tester->test(new stdClass));
    }

    public function testRegExpFrom()
    {
        $tester = $this->tester->parse("regexp:/^hello_world$/");

        static::assertTrue($tester->test("hello_world"));

        static::assertFalse($tester->test("other string"));
        static::assertFalse($tester->test(new stdClass));
    }

    public function testRegExpHasComma()
    {
        $tester = $this->tester->parse("regexp:/^\\d{3,5}$/");

        static::assertTrue($tester->test("100"));
        static::assertTrue($tester->test("1000"));
        static::assertTrue($tester->test("10000"));

        static::assertFalse($tester->test("10"));
        static::assertFalse($tester->test('100000'));
    }
}
