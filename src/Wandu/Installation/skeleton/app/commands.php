<?php
use Wandu\Console\Commands\PsyshCommand;
use Wandu\Database\Migrator\Commands\MigrateCommand;
use Wandu\Database\Migrator\Commands\MigrateCreateCommand;
use Wandu\Database\Migrator\Commands\MigrateDownCommand;
use Wandu\Database\Migrator\Commands\MigrateRollbackCommand;
use Wandu\Database\Migrator\Commands\MigrateStatusCommand;
use Wandu\Database\Migrator\Commands\MigrateUpCommand;
use Wandu\Event\Commands\ListenCommand;
use Wandu\Event\Commands\PingCommand;

return [
    'sh' => PsyshCommand::class,

    'event:listen' => ListenCommand::class,
    'event:ping' => PingCommand::class,
    
    'migrate:create' => MigrateCreateCommand::class,
    'migrate:status' => MigrateStatusCommand::class,
    'migrate' => MigrateCommand::class,
    'migrate:rollback' => MigrateRollbackCommand::class,
    'migrate:up' => MigrateUpCommand::class,
    'migrate:down' => MigrateDownCommand::class,
];
