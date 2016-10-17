<?php
namespace Wandu\Database\Migrator\Commands;

use Wandu\Console\Command;
use Wandu\Console\Exception\ConsoleException;
use RuntimeException;
use Wandu\Database\Migrator\MigrateManager;

class MigrateDownCommand extends Command
{
    /** @var string */
    protected $description = 'Execute targeted rollback.';

    /** @var array */
    protected $arguments = [
        'id' => 'the migrate id for the rollback',
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
            $this->manager->down($id);
        } catch (RuntimeException $e) {
            throw new ConsoleException("<error>Error</error> {$e->getMessage()}");
        }
        $this->output->writeln("<info>rollback</info> {$id}");
    }
}
