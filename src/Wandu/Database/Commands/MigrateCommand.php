<?php
namespace Wandu\Database\Commands;

use Wandu\Console\Command;
use Wandu\Database\Migrator\MigrateManager;

class MigrateCommand extends Command
{
    /** @var string */
    protected $description = 'Run migrate.';

    /** @var \Wandu\Database\Migrator\MigrateManager */
    protected $manager;

    public function __construct(MigrateManager $manager)
    {
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
