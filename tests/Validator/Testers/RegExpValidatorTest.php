<?php
namespace Wandu\Validator\Rules;

use PHPUnit\Framework\TestCase;
use stdClass;
use Wandu\Validator\TesterLoader;

class RegExpValidatorTest extends TestCase 
{
    /** @var \Wandu\Validator\TesterLoader */
    protected $tester;

    public function setUp()
    {
        $this->tester = new TesterLoader();
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
        $tester = $this->tester->load("regexp:/^hello_world$/");

        static::assertTrue($tester->test("hello_world"));

        static::assertFalse($tester->test("other string"));
        static::assertFalse($tester->test(new stdClass));
    }

    public function testRegExpHasComma()
    {
        $tester = $this->tester->load("regexp:/^\\d{3,5}$/");

        static::assertTrue($tester->test("100"));
        static::assertTrue($tester->test("1000"));
        static::assertTrue($tester->test("10000"));

        static::assertFalse($tester->test("10"));
        static::assertFalse($tester->test('100000'));
    }
}
