<?php
namespace Wandu\Math\Foundation
{
    use Wandu\Math\Foundation\Set\HashSet;

    /**
     * @param ...$items
     * @return \Wandu\Math\Foundation\SetInterface
     */
    function set(...$items)
    {
        return new HashSet($items);
    }
}

namespace Wandu\Math\LinearAlgebra
{
    /**
     * @param ...$items
     * @return \Wandu\Math\LinearAlgebra\Vector
     */
    function vector(...$items)
    {
        return new Vector($items);
    }

    /**
     * @param array $items
     * @return \Wandu\Math\LinearAlgebra\Matrix
     */
    function matrix(array $items = [])
    {
        $rowSize = count($items);
        $colSize = array_reduce($items, function ($carry, $cols) {
            return max($carry, count($cols));
        }, 0);

        return new Matrix($rowSize, $colSize, $items);
    }
}
