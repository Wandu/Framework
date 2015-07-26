<?php
namespace Wandu\Router;

interface HandlerMapperInterface
{
    /**
     * @param string $name
     * @return callable
     */
    public function mapHandler($name);

    /**
     * @param string $name
     * @return callable
     */
    public function mapMiddleware($name);
}
