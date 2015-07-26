<?php
namespace Wandu\Router;

interface MapperInterface
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
