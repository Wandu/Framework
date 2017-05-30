<?php
namespace Wandu\Validator\Testers;

use Egulias\EmailValidator\Validation\DNSCheckValidation;
use PHPUnit\Framework\TestCase;
use Wandu\Validator\TesterFactory;
use function Wandu\Validator\tester;

class EmailTest extends TestCase 
{
    /** @var \Wandu\Validator\TesterFactory */
    protected $tester;

    public function setUp()
    {
        $this->tester = new TesterFactory();
    }

    public function testEmail()
    {
        static::assertTrue($this->tester->parse("email")->test('im@wani.kr'));
        static::assertTrue($this->tester->parse("email")->test('im+kr@wani.kr'));
        static::assertTrue($this->tester->parse("email")->test('i.m@wani.kr'));

        static::assertFalse($this->tester->parse("email")->test(111111));
        static::assertFalse($this->tester->parse("email")->test('im@'));
        static::assertFalse($this->tester->parse("email")->test([]));
    }
    
    public function testEmailWithOtherValidation()
    {
        static::assertTrue(tester('email', new DNSCheckValidation())->test('im@wani.kr'));
        static::assertFalse(tester('email', new DNSCheckValidation())->test('im@wani.nothing'));
    }
}
