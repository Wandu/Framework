<?php
namespace Wandu\Database\Support;

class Helper
{
    /**
     * @see http://stackoverflow.com/a/1589535
     * @param string $text
     * @return string
     */
    public static function camelCaseToUnderscore($text)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $text));
    }
    
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
     * @param string $itemPrefix
     * @param string $itemSuffix
     * @return string
     */
    public static function arrayImplode($glue, $input, $itemPrefix = '', $itemSuffix = '')
    {
        if (!count($input)) {
            return '';
        }
        return $itemPrefix . implode($itemSuffix . $glue . $itemPrefix, $input) . $itemSuffix;
    }
}
