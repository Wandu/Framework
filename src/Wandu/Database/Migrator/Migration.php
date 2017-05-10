<?php
namespace Wandu\Database\Migrator;

use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Contracts\Migrator\MigrationInterface;
use Wandu\Database\Manager;

abstract class Migration implements MigrationInterface
{
    /** @var string */
    protected $connection = 'default';

    /** @var \Wandu\Database\Manager */
    protected $manager;

    /**
     * @param \Wandu\Database\Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->migrate($this->manager->connection($this->connection));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->rollback($this->manager->connection($this->connection));
    }

    /**
     * @param \Wandu\Database\Contracts\ConnectionInterface $connection
     */
    abstract public function migrate(ConnectionInterface $connection);

    /**
     * @param \Wandu\Database\Contracts\ConnectionInterface $connection
     */
    abstract public function rollback(ConnectionInterface $connection);
}
