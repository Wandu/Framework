<?php
namespace Wandu\Service\Eloquent;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Builder;
use Wandu\Migrator\Configuration;
use Wandu\Migrator\Contracts\Migration as MigrationContract;

abstract class Migration implements MigrationContract 
{
    /** @var \Illuminate\Database\Capsule\Manager */
    protected $manager;
 
    /** @var \Wandu\Database\Configuration */
    protected $config;
    
    public function __construct(Manager $manager, Configuration $config)
    {
        $this->manager = $manager;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->migrate($this->manager->getConnection($this->config->getConnection())->getSchemaBuilder());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->rollback($this->manager->getConnection($this->config->getConnection())->getSchemaBuilder());
    }

    /**
     * @param \Illuminate\Database\Schema\Builder $schema
     */
    abstract public function migrate(Builder $schema);

    /**
     * @param \Illuminate\Database\Schema\Builder $schema
     */
    abstract public function rollback(Builder $schema);
}
