<?php
namespace Wandu\DI;

interface ServiceProviderInterface
{
    /**
     * @param \Wandu\DI\ContainerInterface $app
     * @return self
     */
    public function register(ContainerInterface $app);
}
