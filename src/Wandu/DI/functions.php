<?php
namespace Wandu\DI;

/**
 * @return \Wandu\DI\ContainerInterface
 */
function container()
{
    return Container::$instance;
}
