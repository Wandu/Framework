<?php
namespace Wandu\DI\Stub;

class Invoker
{
    public function __invoke(DepInterface $dependency)
    {
        return 'invoke with';
    }
}
