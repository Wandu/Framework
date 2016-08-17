<?php
namespace Wandu\Math\LinearAlgebra;

use InvalidArgumentException;
use Closure;

class Vector
{
    /**
     * @param int $size
     * @return static
     */
    public static function zeros($size)
    {
        return new static(array_pad([], $size, 0));
    }

    /** @var array */
    protected $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Vector(" . implode(", ", $this->items) . ")";
    }

    /**
     * @param \Wandu\Math\LinearAlgebra\Vector $other
     * @return number
     */
    public function dot(Vector $other)
    {
        $this->checkCalculatable($other);
        $result = 0;
        foreach ($other->items as $key => $value) {
            $result += $value * $this->items[$key];
        }
        return $result;
    }

    /**
     * @param \Wandu\Math\LinearAlgebra\Vector $other
     * @return \Wandu\Math\LinearAlgebra\Vector
     */
    public function add(Vector $other)
    {
        $this->checkCalculatable($other);
        return $other->map(function ($item, $key) {
            return $item + $this->items[$key];
        });
    }

    protected function checkCalculatable(Vector $other)
    {
        if (count($this->items) !== count($other->items)) {
            throw new InvalidArgumentException('vector size is difference.');
        }
    }

    /**
     * @param \Wandu\Math\LinearAlgebra\Vector $other
     * @return bool
     */
    public function equal(Vector $other)
    {
        return $this->items === $other->items;
    }

    /**
     * @param number $other
     * @return \Wandu\Math\LinearAlgebra\Vector
     */
    public function multiplyWithScalar($other)
    {
        return $this->map(function ($item) use ($other) {
            return $item * $other;
        });
    }

    /**
     * @param \Closure $handler
     * @return \Wandu\Math\LinearAlgebra\Vector
     */
    public function map(Closure $handler)
    {
        $new = [];
        foreach ($this->items as $key => $item) {
            $new[$key] = $handler->__invoke($item, $key);
        }
        return new Vector($new);
    }
}
