<?php
namespace Wandu\Validator\Rules;

use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Wandu\Validator\ValidatorTestCase;
use function Wandu\Validator\validator;

class EmailValidatorTest extends ValidatorTestCase
{
    public function testEmail()
    {
        $this->assertSame(validator()->email(), validator()->email());

        validator()->email()->assert('im@wani.kr');
        validator()->email()->assert('im+kr@wani.kr');
        validator()->email()->assert('i.m@wani.kr');

        $this->assertInvalidValueException(function () {
            validator()->email()->assert(111111);
        }, [
            'email',
        ]);
        $this->assertInvalidValueException(function () {
            validator()->email()->assert('im@');
        }, [
            'email',
        ]);
        $this->assertInvalidValueException(function () {
            validator()->email()->assert([]);
        }, [
            'email',
        ]);
    }
    
    public function testEmailWithOtherValidation()
    {
        $this->assertNotSame(
            validator()->email(new DNSCheckValidation()),
            validator()->email(new DNSCheckValidation())
        );
        
        validator()->email(new DNSCheckValidation())->assert('im@wani.kr');

        $this->assertInvalidValueException(function () {
            validator()->email(new DNSCheckValidation())->assert('im@wani.nothing');
        }, [
            'email',
        ]);
    }
}
