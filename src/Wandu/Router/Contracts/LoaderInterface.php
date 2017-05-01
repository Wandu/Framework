<?php
namespace Wandu\Router\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface LoaderInterface
{
    /**
     * @param string $className
     * @return \Wandu\Router\Contracts\MiddlewareInterface
     */
    public function middleware($className): MiddlewareInterface;

    /**
     * @param string $className
     * @param string $methodName
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return mixed
     */
    public function execute($className, $methodName, ServerRequestInterface $request);
}
