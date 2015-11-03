<?php
namespace Wandu\DI\Stub\Boot;

interface ProviderCheckable
{
    public function register();
    public function boot();
}
