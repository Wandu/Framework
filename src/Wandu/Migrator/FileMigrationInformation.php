<?php
namespace Wandu\Migrator;

use SplFileInfo;
use Wandu\Migrator\Contracts\MigrationInformation;

class FileMigrationInformation implements MigrationInformation
{
    /** @var \SplFileInfo */
    protected $fileInfo;

    /**
     * @param \SplFileInfo $fileInfo
     */
    public function __construct(SplFileInfo $fileInfo)
    {
        $this->fileInfo = $fileInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return substr($this->fileInfo->getFilename(), 14, -4);
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return substr($this->fileInfo->getFilename(), 0, 13);
    }

    public function loadMigrationFile()
    {
        require_once $this->fileInfo->__toString();
    }
}
