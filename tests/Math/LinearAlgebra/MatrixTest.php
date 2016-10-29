<?php
namespace Wandu\Math\LinearAlgebra;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use function Wandu\Math\LinearAlgebra\matrix;

class MatrixTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultUsing()
    {
        // use function \Wandu\Math\LinearAlgebra\matrix;
        $matrix = matrix([
            [1, 2],
            [2, 3],
        ]);

        $this->assertEquals(matrix([
            [3, 6],
            [6, 9],
        ]), $matrix->multiply(3));
    }

    public function testConstruct()
    {
        $matrix = new Matrix(2, 4);

        $this->assertEquals(<<<MAT
Matrix(
  0, 0, 0, 0
  0, 0, 0, 0
)
MAT
            , $matrix->__toString());

        $matrix = new Matrix(2, 4, [0 => [2 => 1]]);

        $this->assertEquals(<<<MAT
Matrix(
  0, 0, 1, 0
  0, 0, 0, 0
)
MAT
            , $matrix->__toString());

        $this->assertEquals(<<<MAT
Matrix(
  0, 0, 2, 0
  0, 0, 0, 0
)
MAT
            , matrix([[0,0,2,0],[0,0,0,0]])->__toString());

    }

    public function testEqual()
    {
        $this->assertEquals(matrix([
            [0,0],
            [0,0],
        ]), Matrix::zeros(2, 2));
    }

    public function testGetNVector()
    {
        $matrix = matrix([
            [1, 2, 3, 4],
            [5, 6, 7, 8],
            [5, 6, 7, 8],
            [3, 4, 5, 6],
        ]);

        $this->assertEquals(vector(5, 6, 7, 8), $matrix->getRowVector(2));
        $this->assertEquals(vector(3, 7, 7, 5), $matrix->getColVector(2));
    }

    public function testTranspose()
    {
        $matrix = matrix([
            [1, 2, 3, 4],
            [2, 3, 4, 5],
        ]);
        $this->assertTrue($matrix->transpose()->equal(matrix([
            [1, 2],
            [2, 3],
            [3, 4],
            [4, 5],
        ])));
        $this->assertEquals(matrix([
            [1, 2],
            [2, 3],
            [3, 4],
            [4, 5],
        ]), $matrix->transpose());
    }

    public function testMultiWithScalar()
    {
        $matrix = matrix([
            [1, 2],
            [5, 6],
        ]);

        $this->assertEquals(matrix([
            [3, 6],
            [15, 18],
        ]), $matrix->multiplyWithScalar(3));
    }

    public function testMultiWithMatrix()
    {
        $matrix = matrix([
            [1, 2],
            [5, 6],
        ]);

        try {
            $matrix->multiply(matrix([[3,3,3,],[3,3,3,],[3,3,3,],]));
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('cannot calculate, because of size.', $e->getMessage());
        }

        $this->assertEquals(matrix([
            [5],
            [17],
        ]), $matrix->multiplyWithMatrix(matrix([
            [1],
            [2]
        ])));
    }
}
