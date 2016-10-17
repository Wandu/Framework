<?php
namespace Wandu\Database\Migrator\Commands;

use Wandu\Console\Command;
use Wandu\Database\Migrator\MigrateManager;

class MigrateRollbackCommand extends Command
{
    /** @var string */
    protected $description = 'Run rollback.';

    /** @var array */
    protected $arguments = [
        'until?' => 'the migrate id for the rollback',
    ];

    /** @var \Wandu\Database\Migrator\MigrateManager */
    protected $manager;

    /**
     * @param \Wandu\Database\Migrator\MigrateManager $manager
     */
    public function __construct(MigrateManager $manager)
    {
        $this->manager = $manager;
    }
    
    public function execute()
    {
        $untilMigrationId = $this->input->getArgument('until');
        
        /** @var \Wandu\Database\Migrator\MigrationContainer[] $migrations */
        $migrations = array_reverse($this->manager->getMigrations());

        if (!count($migrations)) {
            $this->output->writeln("<comment>there is no migration to rollback.</comment>");
            return 2;
        }
        foreach ($migrations as $migration) {
            if (!$migration->isApplied()) {
                continue;
            }
            $this->manager->down($migration->getId());
            if ($migration->getId() === $untilMigrationId || !$untilMigrationId) {
                break;
            }
        }
    }
}
