<?php
namespace Wandu\Database\Console;

class MigrateCommand extends AbstractMigrateCommand
{
    /** @var string */
    protected $description = 'Run migrate.';
    
    public function execute()
    {
        $appliedIds = $this->getAppliedIds();
        if (count($appliedIds)) {
            $lastId = array_pop($appliedIds);
        } else {
            $lastId = null;
        }
        $migrationFiles = $this->getAllMigrationFiles();
        $isNoMigration = true;
        foreach ($migrationFiles as $fileName) {
            $id = $this->getMigrationIdFromFileName($fileName);
            if ($lastId === null) {
                $isNoMigration = false;
                $this->migrateById($id);
                $this->output->writeln("<info>migrate</info> {$id}");
            } elseif ($lastId === $id) {
                $lastId = null;
            }
        }
        
        if ($isNoMigration) {
            $this->output->writeln("<comment>there is no migration to migrate.</comment>");
        }
    }
}
