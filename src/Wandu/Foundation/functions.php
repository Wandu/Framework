<?php
namespace Wandu\Event
{
    use function Wandu\DI\container;

    /**
     * @param string|object $event
     */
    function trigger($event)
    {
        container()->get(EventEmitter::class)->trigger($event);
    }
}

namespace Wandu\Foundation
{
    use Wandu\Config\Contracts\Config;
    use function Wandu\DI\container;

    /**
     * @deprecated use function Wandu\DI\container
     * @return \Wandu\DI\ContainerInterface
     */
    function app()
    {
        return container();
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    function config($name, $default = null)
    {
        return container()->get(Config::class)->get($name, $default);
    }
}
