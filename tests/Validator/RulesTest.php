<?php
namespace Wandu\Validator;

class RulesTest extends ValidatorTestCase
{
    public function testInteger()
    {
        validator()->integer()->assert(30);
        $this->assertInvalidValueException(function () {
            validator()->integer()->assert("30");
        }, [
            'integer',
        ]);
    }

    public function testString()
    {
        validator()->string()->assert('30');
        $this->assertInvalidValueException(function () {
            validator()->string()->assert(30);
        }, [
            'string',
        ]);
    }
    
    public function testMin()
    {
        validator()->min(5)->assert(100);
        validator()->min(5)->assert(6);
        validator()->min(5)->assert(5);

        validator()->min(5)->assert('100');
        validator()->min(5)->assert('6');
        validator()->min(5)->assert('5');

        $this->assertInvalidValueException(function () {
            validator()->min(5)->assert(4);
        }, [
            'min:5',
        ]);

        $this->assertInvalidValueException(function () {
            validator()->min(5)->assert('4');
        }, [
            'min:5',
        ]);
    }

    public function testMax()
    {
        validator()->max(5)->assert(0);
        validator()->max(5)->assert(4);
        validator()->max(5)->assert(5);

        validator()->max(5)->assert('0');
        validator()->max(5)->assert('4');
        validator()->max(5)->assert('5');

        $this->assertInvalidValueException(function () {
            validator()->max(5)->assert(6);
        }, [
            'max:5',
        ]);

        $this->assertInvalidValueException(function () {
            validator()->max(5)->assert('6');
        }, [
            'max:5',
        ]);
    }

    public function testLengthMin()
    {
        validator()->lengthMin(5)->assert('aaaaaaa');
        validator()->lengthMin(5)->assert('aaaaaa');
        validator()->lengthMin(5)->assert('aaaaa');

        validator()->lengthMin(5)->assert(1111111);
        validator()->lengthMin(5)->assert(111111);
        validator()->lengthMin(5)->assert(11111);

        validator()->lengthMin(5)->assert([1, 2, 3, 4, 5, 6, 7, ]);
        validator()->lengthMin(5)->assert([1, 2, 3, 4, 5, 6, ]);
        validator()->lengthMin(5)->assert([1, 2, 3, 4, 5, ]);

        $this->assertInvalidValueException(function () {
            validator()->lengthMin(5)->assert('aaaa');
        }, [
            'length_min:5',
        ]);

        $this->assertInvalidValueException(function () {
            validator()->lengthMin(5)->assert(1111);
        }, [
            'length_min:5',
        ]);

        $this->assertInvalidValueException(function () {
            validator()->lengthMin(5)->assert([1, 2, 3, 4, ]);
        }, [
            'length_min:5',
        ]);
    }

    public function testLengthMax()
    {
        validator()->lengthMax(5)->assert('');
        validator()->lengthMax(5)->assert('aaaa');
        validator()->lengthMax(5)->assert('aaaaa');

        validator()->lengthMax(5)->assert(1);
        validator()->lengthMax(5)->assert(1111);
        validator()->lengthMax(5)->assert(11111);

        validator()->lengthMax(5)->assert([1, ]);
        validator()->lengthMax(5)->assert([1, 2, 3, 4, ]);
        validator()->lengthMax(5)->assert([1, 2, 3, 4, 5, ]);

        $this->assertInvalidValueException(function () {
            validator()->lengthMax(5)->assert('aaaaaa');
        }, [
            'length_max:5',
        ]);

        $this->assertInvalidValueException(function () {
            validator()->lengthMax(5)->assert(111111);
        }, [
            'length_max:5',
        ]);

        $this->assertInvalidValueException(function () {
            validator()->lengthMax(5)->assert([1, 2, 3, 4, 5, 6, ]);
        }, [
            'length_max:5',
        ]);
    }
}
