<?php
namespace Wandu\Caster\Caster;

use Wandu\Caster\CasterInterface;

class FloatCaster implements CasterInterface
{
    /**
     * {@inheritdoc}
     */
    public function cast($value)
    {
        if ($value === null) {
            return (double) 0;
        }
        return (double) $value;
    }
}
