<?php
namespace Wandu\Migrator\Commands;

use Wandu\Console\Command;
use Wandu\Migrator\Migrator;
use RuntimeException;

class StatusCommand extends Command
{
    /** @var string */
    protected $description = 'Show the status of all migrations.';
    
    /** @var \Wandu\Migrator\Migrator */
    protected $manager;

    /**
     * @param \Wandu\Migrator\Migrator $manager
     */
    public function __construct(Migrator $manager) {
        $this->manager = $manager;
    }

    public function execute()
    {
        $this->output->writeln(' STATUS   MIGRATION ID   MIGRATION NAME');
        $this->output->writeln('----------------------------------------');
        foreach ($this->manager->getMigrationInformations() as $migration) {
            $row = $this->manager->isApplied($migration)
                ? sprintf(" <info>%6s</info>", 'up')
                : sprintf(" <error>%6s</error>", 'down');
            $row .= "   {$migration->getId()}  ";
            try {
                $row .= sprintf("<comment>%s</comment>", $migration->getName());
            } catch (RuntimeException $e) {
                $row .= sprintf("<error>%s</error>", "unknown");
            }
            $this->output->writeln($row);
        }
    }
}
