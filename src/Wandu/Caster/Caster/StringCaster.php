<?php
namespace Wandu\Caster\Caster;

use Wandu\Caster\CasterInterface;

class StringCaster implements CasterInterface 
{
    /**
     * {@inheritdoc}
     */
    public function cast($value)
    {
        if ($value === null || (is_array($value) && count($value) === 0)) {
            return '';
        }
        if ($value === true) return 'true';
        if ($value === false) return 'false';
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }
        return (string) $value;
    }
}
