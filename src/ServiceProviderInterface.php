<?php
namespace Wandu\DI;

use ArrayAccess;

interface ServiceProviderInterface
{
    /**
     * @param \Wandu\DI\ContainerInterface $app
     * @param \ArrayAccess $config
     * @return self
     */
    public function register(ContainerInterface $app, ArrayAccess $config = null);
}
