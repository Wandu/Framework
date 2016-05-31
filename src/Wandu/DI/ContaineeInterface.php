<?php
namespace Wandu\DI;

interface ContaineeInterface
{
    /**
     * @return mixed
     */
    public function get();

    /**
     * @return \Wandu\DI\ContaineeInterface
     */
    public function freeze();

    /**
     * @return \Wandu\DI\ContaineeInterface
     */
    public function asFactory();
}
