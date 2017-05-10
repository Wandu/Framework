<?php
namespace Wandu\DI\Contracts;

use Wandu\DI\ContainerInterface;

interface ResolverInterface
{
    /**
     * @param \Wandu\DI\ContainerInterface $container
     * @param array $arguments
     * @return mixed
     */
    public function resolve(ContainerInterface $container, array $arguments = []);
}
