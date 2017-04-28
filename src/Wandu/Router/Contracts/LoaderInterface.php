<?php
namespace Wandu\Router\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface ClassLoaderInterface
{
    /**
     * @param string $className
     * @return object
     */
    public function create($className);

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param object $object
     * @param string $methodName
     * @return mixed
     */
    public function call(ServerRequestInterface $request, $object, $methodName);
}
