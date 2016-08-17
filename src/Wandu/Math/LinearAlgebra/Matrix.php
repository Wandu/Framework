<?php
namespace Wandu\Math\LinearAlgebra;

use InvalidArgumentException;
use Closure;

class Matrix
{
    /**
     * @param int $rowSize
     * @param int $colSize
     * @return static
     */
    public static function zeros($rowSize, $colSize)
    {
        return new static($rowSize, $colSize);
    }

    /** @var int */
    protected $rowSize;

    /** @var int */
    protected $colSize;

    /** @var array */
    protected $items = [];

    /**
     * Matrix constructor.
     * @param int $rowSize
     * @param int $colSize
     * @param array $items
     */
    public function __construct($rowSize, $colSize, array $items = [])
    {
        $this->rowSize = $rowSize;
        $this->colSize = $colSize;
        $this->items = $this->removeSparse($rowSize, $colSize, $items);
    }

    protected function removeSparse($rowSize, $colSize, array $items = [])
    {
        $cleanedItems = [];
        for ($rowIndex = 0; $rowIndex < $rowSize; $rowIndex++) {
            $row = [];
            for ($colIndex = 0; $colIndex < $colSize; $colIndex++) {
                if (isset($items[$rowIndex][$colIndex]) && $items[$rowIndex][$colIndex]) {
                    $row[$colIndex] = $items[$rowIndex][$colIndex];
                }
            }
            if (count($row)) {
                $cleanedItems[$rowIndex] = $row;
            }
        }
        return $cleanedItems;
    }

    public function __toString()
    {
        $result = "Matrix(\n";
        for ($rowIndex = 0; $rowIndex < $this->rowSize; $rowIndex++) {
            $cols = [];
            for ($colIndex = 0; $colIndex < $this->colSize; $colIndex++) {
                $cols[] = isset($this->items[$rowIndex][$colIndex]) ?
                    $this->items[$rowIndex][$colIndex] :
                    0;
            }
            $result .= '  ' . implode(', ', $cols) . "\n";
        }
        $result .= ')';
        return $result;
    }

    /**
     * @return int
     */
    public function getRowSize()
    {
        return $this->rowSize;
    }

    /**
     * @return int
     */
    public function getColSize()
    {
        return $this->colSize;
    }

    public function equal($matrix)
    {
        if (!($matrix instanceof Matrix)) {
            return false;
        }
        return $matrix->each(function ($item, $rowIndex, $colIndex) {
            return $this->items[$rowIndex][$colIndex] == $item;
        });
    }

    /**
     * @param int $index
     * @return \Wandu\Math\LinearAlgebra\Vector
     */
    public function getRowVector($index)
    {
        return new Vector($this->items[$index]);
    }

    public function getColVector($index)
    {
        $new = [];
        foreach ($this->items as $row) {
            $new[] = $row[$index];
        }
        return new Vector($new);
    }

    public function multiply($other)
    {
        if ($other instanceof Matrix) {
            return $this->multiplyWithMatrix($other);
        }
        if (is_numeric($other)) {
            return $this->multiplyWithScalar($other);
        }
        throw new InvalidArgumentException('unsupported type.');
    }

    /**
     * @param $other
     * @return \Wandu\Math\LinearAlgebra\Matrix
     */
    public function multiplyWithScalar($other)
    {
        return $this->map(function ($item) use ($other) {
            return $item * $other;
        });
    }

    /**
     * @return \Wandu\Math\LinearAlgebra\Matrix
     */
    public function transpose()
    {
        $newItems = array_pad([], $this->colSize, []);
        foreach ($this->items as $rowIndex => $row) {
            foreach ($row as $colIndex => $item) {
                $newItems[$colIndex][$rowIndex] = $item;
            }
        }
        return new static($this->colSize, $this->rowSize, $newItems);
    }

    /**
     * @param \Wandu\Math\LinearAlgebra\Matrix $other
     * @return \Wandu\Math\LinearAlgebra\Matrix
     */
    public function multiplyWithMatrix(Matrix $other)
    {
        if ($this->colSize !== $other->rowSize) {
            throw new InvalidArgumentException('cannot calculate, because of size.');
        }
        $newRowSize = $this->rowSize;
        $newColSize = $other->colSize;
        $newItems = [];
        for ($rowIndex = 0; $rowIndex < $newRowSize; $rowIndex++) {
            for ($colIndex = 0; $colIndex < $newColSize; $colIndex++) {
                $newItems[$rowIndex][$colIndex] = $this->getRowVector($rowIndex)->dot($other->getColVector($colIndex));
            }
        }
        return new static($this->rowSize, $other->colSize, $newItems);
    }

    /**
     * @param \Closure $handler
     * @return \Wandu\Math\LinearAlgebra\Matrix
     */
    public function map(Closure $handler)
    {
        $newItems = array_pad([], $this->rowSize, []);
        foreach ($this->items as $rowIndex => $row) {
            foreach ($row as $colIndex => $item) {
                $newItems[$rowIndex][$colIndex] = $handler->__invoke($item, $rowIndex, $colIndex);
            }
        }
        return new static($this->rowSize, $this->colSize, $newItems);
    }

    /**
     * @param \Closure $handler
     * @return bool
     */
    public function each(Closure $handler)
    {
        for ($rowIndex = 0; $rowIndex < $this->rowSize; $rowIndex++) {
            for ($colIndex = 0; $colIndex < $this->colSize; $colIndex++) {
                $item = isset($this->items[$rowIndex][$colIndex]) ?
                    $this->items[$rowIndex][$colIndex] :
                    0;
                if ($handler->__invoke($item, $rowIndex, $colIndex) === false) {
                    return false;
                }
            }
        }
        return true;
    }
}
