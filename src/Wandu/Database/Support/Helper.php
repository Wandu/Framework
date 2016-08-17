<?php
namespace Wandu\Database\Support;

class Helper
{
    /**
     * @param string $glue
     * @param string $input
     * @param int $multiplier
     * @param string $prefix
     * @param string $suffix
     * @return string
     */
    public static function stringRepeat($glue, $input, $multiplier, $prefix = '', $suffix = '')
    {
        if ($multiplier === 0) {
            return $prefix . $suffix;
        }
        return $prefix . $input . str_repeat($glue . $input, $multiplier - 1) . $suffix;
    }

    /**
     * @param string $glue
     * @param array $input
     * @param string $prefix
     * @param string $suffix
     * @return string
     */
    public static function arrayImplode($glue, $input, $prefix = '', $suffix = '')
    {
        return $prefix . implode($suffix . $glue . $prefix, $input) . $suffix;
    }
}
