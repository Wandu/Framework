<?php
namespace Wandu\DI\Stub;

class Invoker
{
    public function __invoke(Renderable $dependency)
    {
        return 'invoke with';
    }
}
