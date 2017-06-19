<?php
namespace Wandu\Router\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface LoaderInterface
{
    /**
     * @param string $className
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Wandu\Router\Contracts\MiddlewareInterface
     */
    public function middleware(string $className, ServerRequestInterface $request): MiddlewareInterface;

    /**
     * @param string $className
     * @param string $methodName
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return mixed
     */
    public function execute(string $className, string $methodName, ServerRequestInterface $request);
}
