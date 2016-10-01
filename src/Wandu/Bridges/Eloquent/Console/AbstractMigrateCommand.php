<?php
namespace Wandu\Bridges\Eloquent\Console;

use DirectoryIterator;
use Illuminate\Database\Capsule\Manager;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\Console\Command;
use Wandu\Console\Exception\ConsoleException;

abstract class AbstractMigrateCommand extends Command
{
    /** @var \Illuminate\Database\Capsule\Manager */
    protected $manager;
    
    /** @var \Wandu\Config\Contracts\ConfigInterface */
    protected $config;

    /** @var string */
    protected $path;

    /**
     * @param \Illuminate\Database\Capsule\Manager $manager
     * @param \Wandu\Config\Contracts\ConfigInterface $config
     */
    public function __construct(Manager $manager, ConfigInterface $config)
    {
        $this->manager = $manager;
        $this->config = $config;
        $this->path = rtrim(WANDU_PATH . '/' . $this->config->get('database.migration.path'), '/');
    }

    /**
     * @return array
     */
    protected function getAppliedIds()
    {
        if (!file_exists($this->path . '/.migrations.json')) {
            file_put_contents($this->path . '/.migrations.json', json_encode([]));
        }
        return json_decode(file_get_contents($this->path . '/.migrations.json'), true);
    }

    /**
     * @param string $target
     */
    protected function saveToAppliedId($target)
    {
        $history = $this->getAppliedIds();
        $history[] = $target;
        sort($history);
        file_put_contents($this->path . '/.migrations.json', json_encode($history));
    }

    /**
     * @param string $target
     */
    protected function removeFromAppliedId($target)
    {
        $history = [];
        foreach ($this->getAppliedIds() as $id) {
            if ($id === $target) continue;
            $history[] = $id;
        }
        sort($history);
        file_put_contents($this->path . '/.migrations.json', json_encode($history));
    }

    /**
     * @return array
     */
    protected function getAllMigrationFiles()
    {
        $files = [];
        foreach (new DirectoryIterator($this->path) as $file) {
            if ($file->isDot() || $file->isDir() || $file->getFilename()[0] === '.') continue;
            $files[] = $file->getFilename();
        }
        sort($files);
        return $files;
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function getMigrationIdFromFileName($fileName)
    {
        return substr($fileName, 0, 13);
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function getMigrationNameFromFileName($fileName)
    {
        return substr($fileName, 14, -4);
    }
    
    /**
     * @param string $id
     * @return string
     */
    protected function getFileNameFromId($id)
    {
        foreach ($this->getAllMigrationFiles() as $file) {
            if (strpos($file, $id . '_') === 0) return $file;
        }
        return null;
    }

    /**
     * @param string $id
     */
    protected function migrateById($id)
    {
        $fileName = $this->getFileNameFromId($id);
        if (!$fileName) {
            throw new ConsoleException("<error>Error</error> there is no migration id \"{$id}\".");
        }

        require $this->path . '/' . $fileName;
        $migrationName = $this->getMigrationNameFromFileName($fileName);

        call_user_func([new $migrationName($this->manager), 'up']);

        $this->saveToAppliedId($id);
    }

    /**
     * @param string $id
     */
    protected function rollbackById($id)
    {
        $fileName = $this->getFileNameFromId($id);
        if (!$fileName) {
            throw new ConsoleException("<error>Error</error> there is no migration id \"{$id}\".");
        }

        require $this->path . '/' . $fileName;
        $migrationName = $this->getMigrationNameFromFileName($fileName);

        call_user_func([new $migrationName($this->manager), 'down']);

        $this->removeFromAppliedId($id);
    }
}
