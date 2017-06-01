<?php
namespace Wandu\Event
{
    use function Wandu\DI\container;

    /**
     * @param \Wandu\Event\EventInterface $event
     */
    function trigger(EventInterface $event)
    {
        container()->get(Dispatcher::class)->trigger($event);
    }
}

namespace Wandu\Foundation
{
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
        return container()->get('config')->get($name, $default);
    }
}

namespace Wandu\View
{
    use Wandu\View\Contracts\RenderInterface;
    use function Wandu\DI\container;

    /**
     * @param string $template
     * @param array $attributes
     * @param string $basePath
     * @return string
     */
    function render($template, array $attributes = [], $basePath = null)
    {
        return container()->get(RenderInterface::class)->render($template, $attributes, $basePath);
    }
}

namespace Wandu\Router
{
    use function Wandu\DI\container;

    /**
     * @return \Wandu\Router\Dispatcher
     */
    function dispatcher()
    {
        return container()->get(Dispatcher::class);
    }
    
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    function route(string $name, array $attributes = [])
    {
        return dispatcher()->getPath($name, $attributes);
    }
}

namespace Wandu\Validator
{
    use Wandu\Validator\Contracts\Tester;
    use function Wandu\DI\container;

    /**
     * @param string $tester
     * @param array $arguments
     * @return \Wandu\Validator\Contracts\Tester
     */
    function tester(string $tester, array $arguments = []): Tester
    {
        $factory = container()->get(TesterFactory::class);
        if (count($arguments)) {
            return $factory->create($tester, $arguments);
        }
        return $factory->parse($tester);
    }

    /**
     * @param string|\Wandu\Validator\Contracts\Rule $rule
     * @return \Wandu\Validator\Validator
     */
    function validator($rule): Validator
    {
        return container()->get(ValidatorFactory::class)->create($rule);
    }
}
