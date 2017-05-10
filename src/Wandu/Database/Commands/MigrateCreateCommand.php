<?php
namespace Wandu\Database\Commands;

use Wandu\Console\Command;
use Wandu\Database\Migrator\MigrateCreator;

class MigrateCreateCommand extends Command
{
    /** @var string */
    protected $description = 'Create a migration file.';

    /** @var array */
    protected $arguments = [
        'name' => 'the name for the migration',
    ];
    
    /** @var \Wandu\Database\Migrator\MigrateCreator */
    protected $creator;

    public function __construct(MigrateCreator $creator)
    {
        $this->creator = $creator;
    }

    public function execute()
    {
        $name = $this->input->getArgument('name');
        $filePath = $this->creator->create($name);
        $this->output->writeln(
            '<info>create</info> .' . str_replace(getcwd(), '', $filePath)
        );
    }
}
