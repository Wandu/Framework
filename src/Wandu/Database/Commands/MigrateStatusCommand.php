<?php
namespace Wandu\Database\Commands;

use Wandu\Console\Command;
use Wandu\Database\Migrator\MigrateManager;

class MigrateStatusCommand extends Command
{
    /** @var string */
    protected $description = 'Show the status of all migrations.';
    
    /** @var \Wandu\Database\Migrator\MigrateManager */
    protected $manager;

    /**
     * @param \Wandu\Database\Migrator\MigrateManager $manager
     */
    public function __construct(MigrateManager $manager) {
        $this->manager = $manager;
    }

    public function execute()
    {
        $this->output->writeln(' STATUS   MIGRATION ID   MIGRATION NAME');
        $this->output->writeln('----------------------------------------');
        foreach ($this->manager->getMigrations() as $migration) {
            if ($migration->isApplied()) {
                $textFormat = " <info>%6s</info>   %s  <comment>%s</comment>";
            } else {
                $textFormat = " <error>%6s</error>   %s  <comment>%s</comment>";
            }
            $this->output->writeln(sprintf(
                $textFormat,
                $migration->isApplied() ? 'up  ' : 'down ',
                $migration->getId(),
                $migration->getName()
            ));
        }
    }
}
