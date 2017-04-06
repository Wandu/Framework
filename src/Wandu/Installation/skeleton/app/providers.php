<?php
use Wandu\Database\DatabaseServiceProvider;
use Wandu\Database\Migrator\MigratorServiceProvider;
use Wandu\DateTime\DateTimeServiceProvider;
use Wandu\Event\EventServiceProvider;
use Wandu\Http\HttpServiceProvider;
use Wandu\Router\RouterServiceProvider;
use Wandu\View\PhiewServiceProvider;
use WanduSkeleton\ApplicationServiceProvider;

return [
    HttpServiceProvider::class,
    RouterServiceProvider::class,
    EventServiceProvider::class,
    DateTimeServiceProvider::class,
    MigratorServiceProvider::class,
    PhiewServiceProvider::class,
    DatabaseServiceProvider::class,
    ApplicationServiceProvider::class,
];
