<?php
namespace Wandu\Foundation\Kernels;

use Wandu\Foundation\Contracts\KernelInterface;
use Wandu\DI\ContainerInterface;

class NullKernel implements KernelInterface
{
    public function boot(ContainerInterface $app)
    {
    }

    public function execute(ContainerInterface $app)
    {
    }

    public function offsetExists($offset)
    {
    }

    public function offsetGet($offset)
    {
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetUnset($offset)
    {
    }
}
