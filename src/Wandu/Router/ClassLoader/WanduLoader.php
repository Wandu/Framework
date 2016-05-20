<?php
namespace Wandu\Router\ClassLoader;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\DI\ContainerInterface;
use Wandu\Router\Contracts\ClassLoaderInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

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
    public function create($className)
    {
        if (!class_exists($className)) {
            throw new HandlerNotFoundException($className);
        }
        return $this->container->create($className);
    }

    /**
     * {@inheritdoc}
     */
    public function call(ServerRequestInterface $request, $object, $methodName)
    {
        if (!method_exists($object, $methodName) && !method_exists($object, '__call')) {
            throw new HandlerNotFoundException(get_class($object), $methodName);
        }
        if (method_exists($object, $methodName)) {
            return $this->container->call([$object, $methodName], [
                ServerRequestInterface::class => $request,
            ]);
        }
        return $this->container->call([$object, '__call'], [
            $methodName, [
                ServerRequestInterface::class => $request
            ],
        ]);
    }
}
