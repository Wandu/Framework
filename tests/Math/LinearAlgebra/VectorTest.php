<?php
namespace Wandu\Math\LinearAlgebra;

use PHPUnit_Framework_TestCase;
use function Wandu\Math\LinearAlgebra\vector;

class VectorTest extends PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $this->assertEquals('Vector()', vector()->__toString());
        $this->assertEquals('Vector(30, 20)', vector(30, 20)->__toString());
    }

    public function testDot()
    {
        $this->assertEquals(120, vector(30, 20)->dot(vector(2, 3)));
        $this->assertEquals(0, vector(20, 10, 5)->dot(Vector::zeros(3)));
    }

    public function testMultiplyWithScalar()
    {
        $vector1 = vector(2, 3, 4);

        $this->assertEquals(
            vector(60, 90, 120),
            $vector1->multiplyWithScalar(30)
        );
        $this->assertTrue(
            $vector1->multiplyWithScalar(30)->equal(vector(60, 90, 120))
        );
        $this->assertTrue(
            $vector1->equal(vector(2, 3, 4))
        );
    }

    public function testAdd()
    {
        $vector1 = vector(2, 3, 4);

        $this->assertEquals(
            vector(12, 23, 34),
            $vector1->add(vector(10, 20, 30))
        );
        $this->assertTrue(
            $vector1->add(vector(10, 20, 30))->equal(vector(12, 23, 34))
        );
        $this->assertTrue(
            $vector1->equal(vector(2, 3, 4))
        );
    }
}
