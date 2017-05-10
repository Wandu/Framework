<?php
namespace Wandu\Database\Migrator\Commands;

use Wandu\Console\Command;
use Wandu\Database\Migrator\MigrateCreator;
use Wandu\DI\ContainerInterface;

class MigrateCreateCommand extends Command
{
    /** @var string */
    protected $description = 'Create a migration file.';

    /** @var array */
    protected $arguments = [
        'name' => 'the name for the migration',
    ];
    
    /** @var \Wandu\DI\ContainerInterface */
    protected $container;
    
    /** @var \Wandu\Database\Migrator\MigrateCreator */
    protected $creator;

    /**
     * @param \Wandu\DI\ContainerInterface $container
     * @param \Wandu\Database\Migrator\MigrateCreator $creator
     */
    public function __construct(
        ContainerInterface $container,
        MigrateCreator $creator
    ) {
        $this->container = $container;
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
