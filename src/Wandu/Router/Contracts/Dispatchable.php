<?php
namespace Wandu\Router\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface Dispatchable
{
    /**
     * @param \Wandu\Router\Contracts\LoaderInterface $loader
     * @param \Wandu\Router\Contracts\ResponsifierInterface $responsifier
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function dispatch(
        LoaderInterface $loader,
        ResponsifierInterface $responsifier,
        ServerRequestInterface $request
    );
}
