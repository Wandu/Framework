<?php
namespace Wandu\Database\Console;

class MigrateRunCommand extends AbstractMigrateCommand
{
    /** @var string */
    protected $description = 'Run targeted migrate.';
    
    /** @var array */
    protected $arguments = [
        'id' => 'the migrate id for the migration',
    ];
    
    public function execute()
    {
        $id = $this->input->getArgument('id');
        if (!preg_match('/^\d{6}_\d{6}$/', $id)) {
            $this->output->writeln("<error>Error</error> invalid migration id. it must be like 000000_000000.");
            return -1;
        }
        $history = $this->getAppliedIds();
        if (in_array($id, $history)) {
            $this->output->writeln("<error>Error</error> this {$id} is already applied.");
            return -1;
        }
        $fileName = $this->getFileNameFromId($id);
        if (!$fileName) {
            $this->output->writeln("<error>Error</error> there is no migration id \"{$id}\".");
            return -1;
        }
        
        require $this->path . '/' . $fileName;
        $migrationName = $this->getMigrationNameFromFileName($fileName);
        
        call_user_func([new $migrationName($this->manager), 'up']);
        
        $this->saveToAppliedId($id);
    }
}
