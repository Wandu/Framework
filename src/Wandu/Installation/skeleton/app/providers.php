<?php
use Wandu\Database\DatabaseServiceProvider;
use Wandu\Database\Migrator\MigratorServiceProvider;
use Wandu\DateTime\DateTimeServiceProvider;
use Wandu\Event\EventServiceProvider;
use Wandu\Http\HttpServiceProvider;
use Wandu\Router\RouterServiceProvider;
use Wandu\View\PhpViewServiceProvider;
use YourOwnApp\ApplicationServiceProvider;

return [
    HttpServiceProvider::class,
    RouterServiceProvider::class,
    EventServiceProvider::class,
    DateTimeServiceProvider::class,
    MigratorServiceProvider::class,
    PhpViewServiceProvider::class,
    DatabaseServiceProvider::class,
    ApplicationServiceProvider::class,
];
