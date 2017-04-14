<?php
use Wandu\Database\DatabaseServiceProvider;
use Wandu\Database\Migrator\MigratorServiceProvider;
use Wandu\DateTime\DateTimeServiceProvider;
use Wandu\Event\EventServiceProvider;
use Wandu\Http\HttpServiceProvider;
use Wandu\Q\BeanstalkdQueueServiceProvider;
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

    BeanstalkdQueueServiceProvider::class,

    ApplicationServiceProvider::class,
];
