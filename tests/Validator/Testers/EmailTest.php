<?php
namespace Wandu\Validator\Testers;

use Egulias\EmailValidator\Validation\DNSCheckValidation;
use PHPUnit\Framework\TestCase;
use function Wandu\Validator\tester;

class EmailTest extends TestCase 
{
    public function testEmail()
    {
        static::assertTrue(tester("email")->test('im@wani.kr'));
        static::assertTrue(tester("email")->test('im+kr@wani.kr'));
        static::assertTrue(tester("email")->test('i.m@wani.kr'));

        static::assertFalse(tester("email")->test(111111));
        static::assertFalse(tester("email")->test('im@'));
        static::assertFalse(tester("email")->test([]));
    }
    
    public function testEmailWithOtherValidation()
    {
        static::assertTrue(tester('email', new DNSCheckValidation())->test('im@wani.kr'));
        static::assertFalse(tester('email', new DNSCheckValidation())->test('im@wani.nothing'));
    }
}
