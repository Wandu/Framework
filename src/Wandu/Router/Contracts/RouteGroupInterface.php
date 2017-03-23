<?php
namespace Wandu\Router\Contracts;

use Wandu\Router\Router;

interface RouteGroupInterface
{
    /**
     * @param \Wandu\Router\Router $router
     * @return mixed
     */
    public function __invoke(Router $router);
}
