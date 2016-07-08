<?php
namespace Wandu\Router\Contracts;

use Wandu\Router\Router;

interface RoutesInterface
{
    /**
     * @param \Wandu\Router\Router $router
     */
    public function routes(Router $router);
}
