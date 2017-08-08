<?php
namespace Wandu\Migrator\Commands;

use Wandu\Console\Command;
use Wandu\Migrator\Migrator;

class MigrateCommand extends Command
{
    /** @var string */
    protected $description = 'Run migrate.';

    /** @var \Wandu\Migrator\Migrator */
    protected $manager;

    public function __construct(Migrator $manager)
    {
        $this->manager = $manager;
    }

    public function execute()
    {
        $migrations = $this->manager->getMigrationInformations();
        $isNoMigration = true;
        foreach ($migrations as $migration) {
            if (!$this->manager->isApplied($migration)) {
                $isNoMigration = false;
                $this->manager->up($migration->getId());
                $this->output->writeln(sprintf("<info>up</info> %s", $migration->getId()));
            }
        }
        if ($isNoMigration) {
            $this->output->writeln("<comment>there is no migration to migrate.</comment>");
        }
    }
}
