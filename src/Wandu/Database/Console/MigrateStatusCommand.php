<?php
namespace Wandu\Database\Console;

class MigrateStatusCommand extends AbstractMigrateCommand
{
    /** @var string */
    protected $description = 'Show the status of all migrations.';

    public function execute()
    {
        $history = $this->getAppliedIds();

        $migrationFiles = $this->getAllMigrationFiles();

        sort($migrationFiles);

        $this->output->writeln(' STATUS   MIGRATION ID   MIGRATION NAME');
        $this->output->writeln('----------------------------------------');
        foreach ($migrationFiles as $fileName) {
            $migrationId = $this->getMigrationIdFromFileName($fileName);
            $migrationName = $this->getMigrationNameFromFileName($fileName);
            if (in_array($migrationId, $history)) {
                $this->output->writeln(sprintf(
                    " <info>%6s</info>   %s  <comment>%s</comment>",
                    'up',
                    $migrationId,
                    $migrationName
                ));
            } else {
                $this->output->writeln(sprintf(
                    " <error>%6s</error>   %s  <comment>%s</comment>",
                    'down',
                    $migrationId,
                    $migrationName
                ));
            }
        }
    }
}
