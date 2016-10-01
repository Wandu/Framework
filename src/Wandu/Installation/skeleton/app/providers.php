<?php
use Wandu\Bridges\Eloquent\EloquentServiceProvider;
use Wandu\Bridges\Latte\LatteServiceProvider;
use Wandu\Bridges\Monolog\MonologServiceProvider;
use Wandu\DateTime\DateTimeServiceProvider;
use Wandu\Event\EventServiceProvider;
use Wandu\Http\HttpServiceProvider;
use Wandu\Router\RouterServiceProvider;
use YourOwnApp\ApplicationServiceProvider;

return [
    HttpServiceProvider::class,
    RouterServiceProvider::class,
    EventServiceProvider::class,
    MonologServiceProvider::class,
    EloquentServiceProvider::class,
    LatteServiceProvider::class,
    DateTimeServiceProvider::class,
    ApplicationServiceProvider::class,
];
