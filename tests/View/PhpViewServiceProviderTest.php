<?php
namespace Wandu\View;

use Wandu\DI\ServiceProviderInterface;
use Wandu\ServiceProviderTestCase;
use Wandu\View\Contracts\RenderInterface;

class PhpViewServiceProviderTest extends ServiceProviderTestCase
{
    /**
     * {@inheritdoc}
     */
    public function getServiceProvider(): ServiceProviderInterface
    {
        return new PhpViewServiceProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function getRegisterClasses(): array
    {
        return [
            RenderInterface::class,
        ];
    }
}
