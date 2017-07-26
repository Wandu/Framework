<?php
namespace Wandu\Router\Contracts;

use Wandu\Router\RouteCollection;

interface RouteGroupInterface
{
    /**
     * @param \Wandu\Router\RouteCollection $router
     * @return mixed
     */
    public function __invoke(RouteCollection $router);
}
