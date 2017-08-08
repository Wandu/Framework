<?php
namespace Wandu\Migrator;

use DirectoryIterator;
use RuntimeException;
use SplFileInfo;
use InvalidArgumentException;
use Wandu\Migrator\Contracts\Adapter;
use Wandu\Migrator\Contracts\MigrationInformation;
use Wandu\Migrator\Contracts\MigrationTemplate;

class Migrator
{
    /** @var \Wandu\Migrator\Contracts\Adapter */
    protected $adapter;
    
    /** @var \Wandu\Migrator\Contracts\MigrationTemplate */
    protected $template;
    
    /** @var \Wandu\Migrator\Configuration */
    protected $config;

    /**
     * @param \Wandu\Migrator\Contracts\Adapter $adapter
     * @param \Wandu\Migrator\Contracts\MigrationTemplate $template
     * @param \Wandu\Migrator\Configuration $config
     */
    public function __construct(Adapter $adapter, MigrationTemplate $template, Configuration $config)
    {
        $this->adapter = $adapter;
        $this->template = $template;
        $this->config = $config;
    }

    /**
     * @param string $name
     * @return string
     */
    public function createTemplate($name): string
    {
        $fileName = date('ymd_His_') . $name . '.php';
        $filePath = $this->config->getPath() . '/' . $fileName;
        if (file_exists($filePath) || !is_dir($this->config->getPath())) {
            throw new InvalidArgumentException(sprintf('cannot write the file at %s.', $filePath));
        }

        file_put_contents($filePath, $this->template->template($name));
        return $filePath;
    }

    /**
     * @return array|\Wandu\Migrator\Contracts\MigrationInformation[]
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
     * @param \Wandu\Migrator\Contracts\MigrationInformation $information
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
        $this->adapter->addToMigrationTable($migrationId);
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
        $this->adapter->removeFromMigrationTable($migrationId);
    }

    /**
     * @return array|\Wandu\Migrator\FileMigrationInformation[]
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
     * @return \Wandu\Migrator\FileMigrationInformation
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
