<?php
namespace Wandu\DI\Stub\Resolve;

class CallExample
{
    /**
     * @return string
     */
    public static function staticMethod()
    {
        return 'static method';
    }

    /**
     * @return string
     */
    public function instanceMethod()
    {
        return 'instance method';
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        return 'invoke';
    }
}
