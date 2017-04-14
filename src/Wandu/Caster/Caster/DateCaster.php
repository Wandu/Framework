<?php
namespace Wandu\Caster\Caster;

use Wandu\Caster\CasterInterface;
use Wandu\DateTime\Date;

class DateCaster implements CasterInterface
{
    /**
     * {@inheritdoc}
     */
    public function cast($value)
    {
        return new Date($value);
    }
}
