<?php
namespace Wandu\Service\Eloquent;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Psr\Container\ContainerInterface;
use Wandu\Migrator\Configuration;
use Wandu\Migrator\Contracts\Adapter;
use Wandu\Migrator\Contracts\Migration;

class EloquentAdapter implements Adapter  
{
    /** @var \Illuminate\Database\Capsule\Manager */
    protected $manager;
    
    /** @var \Psr\Container\ContainerInterface */
    protected $container;
    
    /** @var \Wandu\Migrator\Configuration */
    protected $config;
    
    public function __construct(Manager $manager, ContainerInterface $container, Configuration $config)
    {
        $this->manager = $manager;
        $this->container = $container;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        if (!$this->hasMigrateTable()) {
            $this->getConnection()->getSchemaBuilder()->create($this->config->getTable(), function (Blueprint $table) {
                $table->string('version', 30);
                $table->index('version');
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAppliedIds(): array
    {
        if (!$this->hasMigrateTable()) {
            return [];
        }
        return $this->createMigrationQuery()->get()->map(function ($item) {
            return $item->version;
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function isApplied($id): bool
    {
        if (!$this->hasMigrateTable()) {
            return false;
        }
        foreach ($this->getAppliedIds() as $appliedId) {
            if ($id == $appliedId) return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationInstance(string $id, string $name): Migration
    {
        return $this->container->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function addToMigrationTable($id)
    {
        $this->createMigrationQuery()->insert([
            'version' => $id,
        ]);
    }

    public function removeFromMigrationTable($id)
    {
        $this->createMigrationQuery()->where([
            'version' => $id,
        ])->delete();
    }

    /**
     * @return bool
     */
    protected function hasMigrateTable()
    {
        try {
            $this->createMigrationQuery()->first();
        } catch (\PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function createMigrationQuery()
    {
        return $this->getConnection()->table($this->config->getTable());
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    protected function getConnection()
    {
        return $this->manager->getConnection($this->config->getConnection());
    }
}
