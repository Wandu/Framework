<?php
namespace Wandu\Caster;

interface CastManagerInterface
{
    /**
     * @param string $type
     * @param \Wandu\Caster\CasterInterface $caster
     */
    public function addCaster(string $type, CasterInterface $caster);

    /**
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    public function cast($value, string $type);
}
