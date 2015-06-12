<?php
namespace Wandu\DI;

interface ServiceProviderInterface
{
    /**
     * @param ContainerInterface $app
     * @return self
     */
    public function register(ContainerInterface $app);
}
