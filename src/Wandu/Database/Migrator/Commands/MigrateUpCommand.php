<?php
namespace Wandu\Database\Migrator\Commands;

use RuntimeException;
use Wandu\Console\Command;
use Wandu\Console\Exception\ConsoleException;
use Wandu\Database\Migrator\MigrateManager;

class MigrateUpCommand extends Command
{
    /** @var string */
    protected $description = 'Execute targeted migration.';
    
    /** @var array */
    protected $arguments = [
        'id' => 'the migrate id for the migration',
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
        $id = $this->input->getArgument('id');
        try {
            $this->manager->up($id);
        } catch (RuntimeException $e) {
            throw new ConsoleException("<error>Error</error> {$e->getMessage()}");
        }
        $this->output->writeln("<info>migrate</info> {$id}");
    }
}
