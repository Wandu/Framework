<?php
namespace Wandu\Database\Console;

class MigrateRunCommand extends MigrateCommandAbstract
{
    /** @var string */
    protected $description = 'Run targeted migrate.';
    
    /** @var array */
    protected $arguments = [
        'target' => 'the migrate target file for the migration',
    ];
    
    public function execute()
    {
        $target = $this->input->getArgument('target');
        if (!preg_match('/^\d{6}_\d{6}$/', $target)) {
            $this->output->writeln("<error>Error</error> invalid target string. it must be like 000000_000000.");
            return -1;
        }
        $history = $this->getHistory();
        if (in_array($target, $history)) {
            $this->output->writeln("<error>Error</error> this {$target} is already run.");
            return -1;
        }
        $migrationFile = $this->getMigrationFile($target);
        if (!$migrationFile) {
            $this->output->writeln("<error>Error</error> there is no migration named {$target}.");
            return -1;
        }
        
        require $this->path . '/' . $migrationFile;
        $className = str_replace('.php', '', str_replace($target . '_', '', $migrationFile));
        
        call_user_func([new $className($this->manager), 'up']);
        
        $this->saveToHistory($target);
    }

    /**
     * @param string $target
     * @return string
     */
    protected function getMigrationFile($target)
    {
        foreach ($this->getAllMigrationFiles() as $file) {
            if (strpos($file, $target . '_') === 0) return $file;
        }
        return null;
    }
}
