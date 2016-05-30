<?php
namespace Wandu\DI;

interface ContaineeInterface
{
    /**
     * @return bool
     */
    public function isFrozen();

    /**
     * @return \Wandu\DI\ContaineeInterface
     */
    public function freeze();
    
    /**
     * @return mixed
     */
    public function create();
}
