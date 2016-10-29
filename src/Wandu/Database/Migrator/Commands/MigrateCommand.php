<?php
namespace Wandu\Database\Migrator\Commands;

use Wandu\Console\Command;
use Wandu\Database\Migrator\MigrateManager;
use Wandu\DI\ContainerInterface;

class MigrateCommand extends Command
{
    /** @var string */
    protected $description = 'Run migrate.';

    /** @var \Wandu\DI\ContainerInterface */
    protected $container;

    /** @var \Wandu\Database\Migrator\MigrateManager */
    protected $manager;

    /**
     * @param \Wandu\DI\ContainerInterface $container
     * @param \Wandu\Database\Migrator\MigrateManager $manager
     */
    public function __construct(ContainerInterface $container, MigrateManager $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
    }

    public function execute()
    {
        $migrations = $this->manager->getMigrations();
        $isNoMigration = true;
        foreach ($migrations as $migration) {
            if (!$migration->isApplied()) {
                $isNoMigration = false;
                $migration->up();
                $this->output->writeln(sprintf("<info>migrate</info> %s", $migration->getId()));
            }
        }
        if ($isNoMigration) {
            $this->output->writeln("<comment>there is no migration to migrate.</comment>");
        }
    }
}
