<?php
namespace Wandu\Bridges\Eloquent\Console;

class MigrateRollbackCommand extends AbstractMigrateCommand
{
    /** @var string */
    protected $description = 'Run rollback.';

    public function execute()
    {
        $appliedIds = $this->getAppliedIds();
        if (count($appliedIds)) {
            $lastId = array_pop($appliedIds);
        } else {
            $this->output->writeln("<comment>there is no migration to rollback.</comment>");
            return 2;
        }
        $this->rollbackById($lastId);
    }
}
