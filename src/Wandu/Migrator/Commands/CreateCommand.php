<?php
namespace Wandu\Migrator\Commands;

use Wandu\Console\Command;
use Wandu\Migrator\Migrator;

class CreateCommand extends Command
{
    /** @var string */
    protected $description = 'Create a migration file.';

    /** @var array */
    protected $arguments = [
        'name' => 'the name for the migration',
    ];
    
    /** @var \Wandu\Migrator\Migrator */
    protected $migrator;

    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
    }

    public function execute()
    {
        $name = $this->input->getArgument('name');
        $filePath = $this->migrator->createTemplate($name);
        $this->output->writeln(
            '<info>create</info> .' . str_replace(getcwd(), '', $filePath)
        );
    }
}
