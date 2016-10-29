<?php
namespace Wandu\Database\Migrator;

use SplFileInfo;
use Wandu\DI\ContainerInterface;

class MigrationContainer
{
    /** @var \SplFileInfo */
    protected $fileInfo;

    /** @var \Wandu\Database\Migrator\MigrateAdapterInterface */
    protected $adapter;
    
    /** @var \Wandu\DI\ContainerInterface */
    protected $container;
    
    /**
     * @param \SplFileInfo $fileInfo
     * @param \Wandu\Database\Migrator\MigrateAdapterInterface $adapter
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function __construct(SplFileInfo $fileInfo, MigrateAdapterInterface $adapter, ContainerInterface $container)
    {
        $this->fileInfo = $fileInfo;
        $this->adapter = $adapter;
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return substr($this->fileInfo->getFilename(), 14, -4);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return substr($this->fileInfo->getFilename(), 0, 13);
    }

    /**
     * @return bool
     */
    public function isApplied()
    {
        return !!$this->adapter->version($this->getId());
    }
    
    public function up()
    {
        require $this->fileInfo->__toString();
        $migrationName = $this->getName();
        $this->container->create($migrationName)->up();
        $this->adapter->initialize();
        $this->adapter->up($this->getId(), file_get_contents($this->fileInfo));
    }
}
