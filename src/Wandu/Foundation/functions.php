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
