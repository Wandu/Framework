<?php
namespace Wandu\Database\Commands;

use Wandu\Console\Command;
use Wandu\Database\Migrator\Migrator;

class MigrateCommand extends Command
{
    /** @var string */
    protected $description = 'Run migrate.';

    /** @var \Wandu\Database\Migrator\Migrator */
    protected $manager;

    public function __construct(Migrator $manager)
    {
        $this->manager = $manager;
    }

    public function execute()
    {
        $migrations = $this->manager->migrations();
        $isNoMigration = true;
        foreach ($migrations as $migration) {
            if (!$migration->isApplied()) {
                $isNoMigration = false;
                $migration->up();
                $this->output->writeln(sprintf("<info>up</info> %s", $migration->getId()));
            }
        }
        if ($isNoMigration) {
            $this->output->writeln("<comment>there is no migration to migrate.</comment>");
        }
    }
}
