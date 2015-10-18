<?php
namespace Wandu\Router\ClassLoader;

use Wandu\DI\ContainerInterface;
use Wandu\Router\Contracts\ClassLoaderInterface;

class WanduLoader implements ClassLoaderInterface
{
    /** @var \Wandu\DI\ContainerInterface */
    protected $container;

    /**
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load($name)
    {
        return $this->container->create($name);
    }
}
