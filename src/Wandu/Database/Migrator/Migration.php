<?php
namespace Wandu\Database\Migrator;

use Wandu\Database\Contracts\Connection;
use Wandu\Database\Contracts\Migrator\MigrationInterface;
use Wandu\Database\DatabaseManager;

abstract class Migration implements MigrationInterface
{
    /** @var string */
    protected $connection = 'default';

    /** @var \Wandu\Database\DatabaseManager */
    protected $manager;

    /**
     * @param \Wandu\Database\DatabaseManager $manager
     */
    public function __construct(DatabaseManager $manager)
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
     * @param \Wandu\Database\Contracts\Connection $connection
     */
    abstract public function migrate(Connection $connection);

    /**
     * @param \Wandu\Database\Contracts\Connection $connection
     */
    abstract public function rollback(Connection $connection);
}
