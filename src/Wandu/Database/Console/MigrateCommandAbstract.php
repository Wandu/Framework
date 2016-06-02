<?php
namespace Wandu\Database\Console;

use DirectoryIterator;
use Illuminate\Database\Capsule\Manager;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\Console\Command;

abstract class MigrateCommandAbstract extends Command
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
    protected function getHistory()
    {
        if (!file_exists($this->path . '/.migrations.json')) {
            file_put_contents($this->path . '/.migrations.json', json_encode([]));
        }
        return json_decode(file_get_contents($this->path . '/.migrations.json'), true);
    }

    /**
     * @param string $target
     */
    protected function saveToHistory($target)
    {
        $history = $this->getHistory();
        $history[] = $target;
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
}
