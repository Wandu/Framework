<?php
namespace Wandu\Router\ClassLoader;

use ArrayAccess;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\ClassLoaderInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

class ArrayAccessLoader extends DefaultLoader implements ClassLoaderInterface
{
    /** @var \ArrayAccess */
    protected $container;

    /**
     * @param \ArrayAccess $container
     */
    public function __construct(ArrayAccess $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ServerRequestInterface $request, $className)
    {
        if (!isset($this->container[$className])) {
            throw new HandlerNotFoundException($className);
        }
        return $this->container[$className];
    }
}
