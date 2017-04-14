<?php
namespace Wandu\Caster\Caster;

use Wandu\Caster\CasterInterface;

class IntegerCaster implements CasterInterface
{
    /**
     * {@inheritdoc}
     */
    public function cast($value)
    {
        if ($value === null) {
            return 0;
        }
        return (int) $value;
    }
}
