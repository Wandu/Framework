<?php
namespace Wandu\Database\Migrator;

use DirectoryIterator;
use RuntimeException;
use SplFileInfo;
use Wandu\Database\Contracts\Migrator\MigrateAdapterInterface;
use Wandu\Database\Contracts\Migrator\MigrationInterface;
use Wandu\DI\ContainerInterface;

class Migrator
{
    /** @var \Wandu\Database\Contracts\Migrator\MigrateAdapterInterface */
    protected $adapter;
    
    /** @var \Wandu\Database\Migrator\Configuration */
    protected $config;

    /** @var \Wandu\DI\ContainerInterface */
    protected $container;
    
    /**
     * @param \Wandu\Database\Contracts\Migrator\MigrateAdapterInterface $adapter
     * @param \Wandu\Database\Migrator\Configuration $config
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function __construct(MigrateAdapterInterface $adapter, Configuration $config, ContainerInterface $container)
    {
        $this->adapter = $adapter;
        $this->config = $config;
        $this->container = $container;
    }

    /**
     * @return array|\Wandu\Database\Migrator\MigrationContainer[]
     */
    public function migrations()
    {
        $migrations = [];
        foreach ($this->getAllMigrationFiles() as $file) {
            $migrations[] = new MigrationContainer($file, $this->adapter, $this->container);
        }
        return $migrations;
    }

    /**
     * @param string $migrationId
     * @return \Wandu\Database\Migrator\MigrationContainer
     */
    public function migration($migrationId)
    {
        foreach ($this->getAllMigrationFiles() as $file) {
            if (strpos($file, $migrationId . '_') !== false) {
                return new MigrationContainer($file, $this->adapter, $this->container);
            }
        }
        throw new RuntimeException("there is no migration id \"{$migrationId}\".");
    }

    /**
     * @param string $migrationId
     */
    public function up($migrationId)
    {
        if (!preg_match('/^\d{6}_\d{6}$/', $migrationId)) {
            throw new RuntimeException("invalid migration id. it must be like 000000_000000.");
        }
        $version = $this->adapter->version($migrationId);
        if ($version) {
            throw new RuntimeException("this {$migrationId} is already applied.");
        }
        
        $this->migration($migrationId)->up();
    }

    /**
     * @param string $migrationId
     */
    public function down($migrationId)
    {
        if (!preg_match('/^\d{6}_\d{6}$/', $migrationId)) {
            throw new RuntimeException("invalid migration id. it must be like 000000_000000.");
        }
        $version = $this->adapter->version($migrationId);
        if (!$version) {
            throw new RuntimeException("this {$migrationId} is not already applied.");
        }

        $this->migration($migrationId)->down();
    }

    /**
     * @return array|\Wandu\Database\Migrator\MigrationContainer[]
     */
    public function migrate()
    {
        $migratedMigrations = [];
        $migrations = $this->migrations();
        foreach ($migrations as $migration) {
            if (!$this->adapter->version($migration->getId())) {
                $migration->up();
                $migratedMigrations[] = $migration;
            }
        }
        return $migratedMigrations;
    }
    
    /**
     * @return array
     */
    protected function getAllMigrationFiles()
    {
        $files = [];
        foreach (new DirectoryIterator($this->config->getPath()) as $file) {
            if ($file->isDot() || $file->isDir() || $file->getFilename()[0] === '.') continue;
            $files[] = $file->getFileInfo();
        }
        usort($files, function (SplFileInfo $file, SplFileInfo $nextFile) {
            if ($file->getFilename() > $nextFile->getFilename()) {
                return 1;
            }
            return $file->getFilename() < $nextFile->getFilename() ? -1 : 0;
        });
        return $files;
    }
}
