<?php
namespace Wandu\Foundation\Contracts;

interface Definition
{
    /**
     * @return \Wandu\DI\ServiceProviderInterface[]
     */
    public function providers(): array;
}
