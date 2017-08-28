<?php
namespace Wandu\Validator\Testers;

use Egulias\EmailValidator\Validation\DNSCheckValidation;
use PHPUnit\Framework\TestCase;
use Wandu\Validator\TesterLoader;

class EmailTest extends TestCase 
{
    /** @var \Wandu\Validator\TesterLoader */
    protected $tester;

    public function setUp()
    {
        $this->tester = new TesterLoader();
    }

    public function testEmail()
    {
        static::assertTrue($this->tester->load("email")->test('im@wani.kr'));
        static::assertTrue($this->tester->load("email")->test('im+kr@wani.kr'));
        static::assertTrue($this->tester->load("email")->test('i.m@wani.kr'));

        static::assertFalse($this->tester->load("email")->test(111111));
        static::assertFalse($this->tester->load("email")->test('im@'));
        static::assertFalse($this->tester->load("email")->test([]));
    }
    
    public function testEmailWithOtherValidation()
    {
        static::assertTrue($this->tester->create('email', [new DNSCheckValidation()])->test('im@wani.kr'));
        static::assertFalse($this->tester->create('email', [new DNSCheckValidation()])->test('im@wani.nothing'));
    }
}
