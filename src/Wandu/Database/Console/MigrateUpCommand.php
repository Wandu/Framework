<?php
namespace Wandu\Database\Console;

use Wandu\Console\Exception\ConsoleException;

class MigrateUpCommand extends AbstractMigrateCommand
{
    /** @var string */
    protected $description = 'Execute targeted migration.';
    
    /** @var array */
    protected $arguments = [
        'id' => 'the migrate id for the migration',
    ];
    
    public function execute()
    {
        $id = $this->input->getArgument('id');
        if (!preg_match('/^\d{6}_\d{6}$/', $id)) {
            throw new ConsoleException("<error>Error</error> invalid migration id. it must be like 000000_000000.");
        }
        $history = $this->getAppliedIds();
        if (in_array($id, $history)) {
            throw new ConsoleException("<error>Error</error> this {$id} is already applied.");
        }
        
        $this->migrateById($id);
        $this->output->writeln("<info>migrate</info> {$id}");
    }
}
