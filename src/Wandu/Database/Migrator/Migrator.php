<?php
namespace Wandu\Database\Migrator;

use DirectoryIterator;
use RuntimeException;
use SplFileInfo;
use Wandu\Database\Migrator\Contracts\Adapter;
use Wandu\Database\Migrator\Contracts\MigrationInformation;
use Wandu\DI\ContainerInterface;

class Migrator
{
    /** @var \Wandu\Database\Migrator\Contracts\Adapter */
    protected $adapter;
    
    /** @var \Wandu\Database\Migrator\Configuration */
    protected $config;

    /** @var \Wandu\DI\ContainerInterface */
    protected $container;
    
    /**
     * @param \Wandu\Database\Migrator\Contracts\Adapter $adapter
     * @param \Wandu\Database\Migrator\Configuration $config
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function __construct(Adapter $adapter, Configuration $config, ContainerInterface $container)
    {
        $this->adapter = $adapter;
        $this->config = $config;
        $this->container = $container;
    }

    /**
     * @return array|\Wandu\Database\Migrator\Contracts\MigrationInformation[]
     */
    public function getMigrationInformations()
    {
        $migrations = [];
        foreach ($this->getAllMigrationFiles() as $file) {
            $migration = new FileMigrationInformation($file);
            $migrations[$migration->getId()] = $migration;
        }
        foreach ($this->adapter->getAppliedIds() as $version) {
            if (!array_key_exists($version, $migrations)) {
                $migrations[$version] = new UnknownMigrationInformation($version);
            }
        }
        ksort($migrations);
        return array_values($migrations);
    }

    /**
     * @param \Wandu\Database\Migrator\Contracts\MigrationInformation $information
     * @return bool
     */
    public function isApplied(MigrationInformation $information): bool
    {
        return $this->adapter->isApplied($information->getId());
    }

    /**
     * @param string $migrationId
     */
    public function up($migrationId)
    {
        if (!preg_match('/^\d{6}_\d{6}$/', $migrationId)) {
            throw new RuntimeException("invalid migration id. it must be like 000000_000000.");
        }
        if ($this->adapter->isApplied($migrationId)) {
            throw new RuntimeException("this {$migrationId} is already applied.");
        }

        $migrationInfo = $this->getFileMigrationInformation($migrationId);
        $migrationInfo->loadMigrationFile();

        $this->adapter->getMigrationInstance($migrationInfo->getId(), $migrationInfo->getName())->up();
        $this->adapter->initialize();
        $this->adapter->up($migrationId);
    }

    /**
     * @param string $migrationId
     */
    public function down($migrationId)
    {
        if (!preg_match('/^\d{6}_\d{6}$/', $migrationId)) {
            throw new RuntimeException("invalid migration id. it must be like 000000_000000.");
        }
        if (!$this->adapter->isApplied($migrationId)) {
            throw new RuntimeException("this {$migrationId} is not already applied.");
        }

        $migrationInfo = $this->getFileMigrationInformation($migrationId);
        $migrationInfo->loadMigrationFile();

        $this->adapter->getMigrationInstance($migrationInfo->getId(), $migrationInfo->getName())->down();
        $this->adapter->initialize();
        $this->adapter->down($migrationId);
    }

    /**
     * @return array|\Wandu\Database\Migrator\FileMigrationInformation[]
     */
    public function migrate()
    {
        $migratedMigrations = [];
        $migrations = $this->getMigrationInformations();
        foreach ($migrations as $migration) {
            if (!$this->adapter->isApplied($migration->getId())) {
                $this->up($migration->getId());
                $migratedMigrations[] = $migration;
            }
        }
        return $migratedMigrations;
    }

    /**
     * @param string $migrationId
     * @return \Wandu\Database\Migrator\FileMigrationInformation
     */
    protected function getFileMigrationInformation($migrationId): FileMigrationInformation
    {
        foreach ($this->getAllMigrationFiles() as $file) {
            if (strpos($file, $migrationId . '_') !== false) {
                return new FileMigrationInformation($file);
            }
        }
        throw new RuntimeException("there is no migration id \"{$migrationId}\".");
    }
    
    /**
     * @return array
     */
    protected function getAllMigrationFiles()
    {
        $files = [];
        if (!is_dir($this->config->getPath())) {
            mkdir($this->config->getPath());
        }
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
