<?php
namespace Wandu\DI;

interface ContaineeInterface
{
    /**
     * @return bool
     */
    public function isFrozen();

    /**
     * @return mixed
     */
    public function create();
}
