<?php
namespace Wandu\DI;

interface ServiceProviderInterface
{
    /**
     * @param \Wandu\DI\ContainerInterface $app
     */
    public function register(ContainerInterface $app);

    /**
     * @param \Wandu\DI\ContainerInterface $app
     */
    public function boot(ContainerInterface $app);
}
