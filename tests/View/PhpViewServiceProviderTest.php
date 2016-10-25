<?php
namespace Wandu\View;

use Wandu\ServiceProviderTestCase;
use Wandu\View\Contracts\RenderInterface;

class PhpViewServiceProviderTest extends ServiceProviderTestCase 
{
    public function getServiceProvider()
    {
        return new PhpViewServiceProvider();
    }
    
    public function getRegisterClasses()
    {
        return [
            RenderInterface::class,
        ];
    }
}
