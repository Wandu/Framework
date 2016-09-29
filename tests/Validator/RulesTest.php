<?php
namespace Wandu\Validator;

class RulesTest extends ValidatorTestCase
{
    public function testBoolean()
    {
        validator()->boolean()->assert(null); // null is always safe.
        
        validator()->boolean()->assert(true);
        validator()->boolean()->assert(false);

        $this->assertInvalidValueException(function () {
            validator()->boolean()->assert(30);
        }, [
            'boolean',
        ]);
        $this->assertInvalidValueException(function () {
            validator()->boolean()->assert(30.0);
        }, [
            'boolean',
        ]);
        $this->assertInvalidValueException(function () {
            validator()->boolean()->assert("30");
        }, [
            'boolean',
        ]);
    }

    public function testInteger()
    {
        validator()->integer()->assert(null); // null is always safe.

        validator()->integer()->assert(30);
        $this->assertInvalidValueException(function () {
            validator()->integer()->assert("30");
        }, [
            'integer',
        ]);
    }

    public function testNumeric()
    {
        validator()->numeric()->assert(null); // null is always safe.

        validator()->numeric()->assert(30);
        validator()->numeric()->assert("30");
        validator()->numeric()->assert(0347123);
        validator()->numeric()->assert("0347123");
        validator()->numeric()->assert(0xfffff);
        validator()->numeric()->assert('0xfffff');
        validator()->numeric()->assert(30.33);
        validator()->numeric()->assert('30.33');

        $this->assertInvalidValueException(function () {
            validator()->numeric()->assert("string");
        }, [
            'numeric',
        ]);
    }

    public function testIntegerable()
    {
        validator()->integerable()->assert(null); // null is always safe.

        validator()->integerable()->assert('30');
        validator()->integerable()->assert(30);
        validator()->integerable()->assert('-30');
        validator()->integerable()->assert(-30);
        validator()->integerable()->assert(0);
        validator()->integerable()->assert('0');

        $this->assertInvalidValueException(function () {
            validator()->integerable()->assert(40.5);
        }, ['integerable',]);
        $this->assertInvalidValueException(function () {
            validator()->integerable()->assert('40.5');
        }, ['integerable',]);
        $this->assertInvalidValueException(function () {
            validator()->integerable()->assert(40.0);
        }, ['integerable',]);
        $this->assertInvalidValueException(function () {
            validator()->integerable()->assert('40.0');
        }, ['integerable',]);
        $this->assertInvalidValueException(function () {
            validator()->integerable()->assert(-40.0);
        }, ['integerable',]);
        $this->assertInvalidValueException(function () {
            validator()->integerable()->assert('-40.0');
        }, ['integerable',]);
        $this->assertInvalidValueException(function () {
            validator()->integerable()->assert(0.0);
        }, ['integerable',]);
        $this->assertInvalidValueException(function () {
            validator()->integerable()->assert('0.0');
        }, ['integerable',]);
        $this->assertInvalidValueException(function () {
            validator()->integerable()->assert('string');
        }, ['integerable',]);
        $this->assertInvalidValueException(function () {
            validator()->integerable()->assert([]);
        }, ['integerable',]);
        $this->assertInvalidValueException(function () {
            validator()->integerable()->assert(new \stdClass());
        }, ['integerable',]);
    }

    public function testFloat()
    {
        validator()->float()->assert(null); // null is always safe.

        validator()->float()->assert(30.1);
        validator()->float()->assert(30.0);

        $this->assertInvalidValueException(function () {
            validator()->float()->assert("30");
        }, [
            'float',
        ]);
        $this->assertInvalidValueException(function () {
            validator()->float()->assert(30);
        }, [
            'float',
        ]);
    }

    public function testFloatable()
    {
        validator()->floatable()->assert(null); // null is always safe.

        validator()->floatable()->assert('30');
        validator()->floatable()->assert(30);
        validator()->floatable()->assert('-30');
        validator()->floatable()->assert(-30);
        validator()->floatable()->assert(0);
        validator()->floatable()->assert('0');

        validator()->floatable()->assert('30.0');
        validator()->floatable()->assert(30.0);
        validator()->floatable()->assert('-30.0');
        validator()->floatable()->assert(-30.0);
        validator()->floatable()->assert('30.5');
        validator()->floatable()->assert(30.5);
        validator()->floatable()->assert('-30.5');
        validator()->floatable()->assert(-30.5);
        validator()->floatable()->assert(0.0);
        validator()->floatable()->assert('0.0');

        $this->assertInvalidValueException(function () {
            validator()->floatable()->assert('string');
        }, ['floatable',]);
        $this->assertInvalidValueException(function () {
            validator()->floatable()->assert([]);
        }, ['floatable',]);
        $this->assertInvalidValueException(function () {
            validator()->floatable()->assert(new \stdClass());
        }, ['floatable',]);
    }

    public function testString()
    {
        validator()->string()->assert(null); // null is always safe.

        validator()->string()->assert('30');
        $this->assertInvalidValueException(function () {
            validator()->string()->assert(30);
        }, [
            'string',
        ]);
    }
    
    public function testStringable()
    {
        validator()->stringable()->assert(null); // null is always safe.

        validator()->stringable()->assert('30');
        validator()->stringable()->assert(30);
        validator()->stringable()->assert(40.5);
        validator()->stringable()->assert('string');
        validator()->stringable()->assert('string');

        $this->assertInvalidValueException(function () {
            validator()->stringable()->assert([]);
        }, [
            'stringable',
        ]);
        $this->assertInvalidValueException(function () {
            validator()->stringable()->assert(new \stdClass());
        }, [
            'stringable',
        ]);
    }

    public function testMin()
    {
        validator()->min(5)->assert(null); // null is always safe.

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
        validator()->max(5)->assert(null); // null is always safe.

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
        validator()->lengthMin(5)->assert(null); // null is always safe.

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
        validator()->lengthMax(5)->assert(null); // null is always safe.

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
