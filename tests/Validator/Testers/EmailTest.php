<?php
namespace Wandu\Validator\Testers;

use Egulias\EmailValidator\Validation\DNSCheckValidation;
use PHPUnit\Framework\TestCase;
use Wandu\Validator\TesterFactory;

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
        static::assertTrue($this->tester->create('email', [new DNSCheckValidation()])->test('im@wani.kr'));
        static::assertFalse($this->tester->create('email', [new DNSCheckValidation()])->test('im@wani.nothing'));
    }
}
