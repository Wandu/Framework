<?php
namespace Wandu\Event\Contracts;

interface Listener
{
    /**
     * @param array $arguments
     */
    public function call(array $arguments = []);
}
