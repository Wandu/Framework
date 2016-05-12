<?php
namespace Wandu\Caster;

interface CasterInterface
{
    /**
     * @param mixed $value
     * @param mixed $type
     * @return mixed
     */
    public function cast($value, $type);
}
