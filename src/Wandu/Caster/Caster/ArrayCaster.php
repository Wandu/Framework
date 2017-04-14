<?php
namespace Wandu\Caster\Caster;

use Wandu\Caster\CasterInterface;

class ArrayCaster implements CasterInterface
{
    /**
     * {@inheritdoc}
     */
    public function cast($value)
    {
        if (is_string($value)) {
            $result = json_decode($value, true);
            if (json_last_error() === \JSON_ERROR_NONE) {
                return (array) $result;
            }
            $result = json_decode("[{$value}]", true);
            if (json_last_error() === \JSON_ERROR_NONE) {
                return (array) $result;
            }
        }
        return (array) $value;
    }
}
