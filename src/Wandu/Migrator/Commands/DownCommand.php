<?php
namespace Wandu\Migrator\Commands;

use Wandu\Console\Command;
use Wandu\Console\Exception\ConsoleException;
use RuntimeException;
use Wandu\Migrator\Migrator;

class DownCommand extends Command
{
    /** @var string */
    protected $description = 'Execute targeted rollback.';

    /** @var array */
    protected $arguments = [
        'id' => 'the migrate id for the rollback',
    ];
    
    /** @var \Wandu\Migrator\Migrator */
    protected $manager;

    /**
     * @param \Wandu\Migrator\Migrator $manager
     */
    public function __construct(Migrator $manager)
    {
        $this->manager = $manager;
    }

    public function execute()
    {
        $id = $this->input->getArgument('id');
        $this->manager->down($id);
        $this->output->writeln("<info>down</info> {$id}");
    }
}
