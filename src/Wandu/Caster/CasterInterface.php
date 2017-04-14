<?php
namespace Wandu\Caster;

interface CasterInterface
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function cast($value);
}
