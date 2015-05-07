<?php
namespace Wandu\DI;

interface ServiceProviderInterface
{
    public function register(Container $app);
}
