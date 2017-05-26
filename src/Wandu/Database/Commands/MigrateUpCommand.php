<?php
namespace Wandu\Database\Commands;

use RuntimeException;
use Wandu\Console\Command;
use Wandu\Console\Exception\ConsoleException;
use Wandu\Database\Migrator\Migrator;

class MigrateUpCommand extends Command
{
    /** @var string */
    protected $description = 'Execute targeted migration.';
    
    /** @var array */
    protected $arguments = [
        'id' => 'the migrate id for the migration',
    ];

    /** @var \Wandu\Database\Migrator\Migrator */
    protected $manager;

    /**
     * @param \Wandu\Database\Migrator\Migrator $manager
     */
    public function __construct(Migrator $manager)
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
        $this->output->writeln("<info>up</info> {$id}");
    }
}