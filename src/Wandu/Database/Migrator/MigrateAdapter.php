<?php
namespace Wandu\Database\Migrator;

use Wandu\Database\Manager;
use Wandu\Database\Query\CreateQuery;

class MigrateAdapter implements MigrateAdapterInterface
{
    /** @var \Wandu\Database\Contracts\ConnectionInterface */
    protected $connection;

    /** @var string */
    protected $tableName;

    public function __construct(Manager $manager, Configuration $config)
    {
        $this->tableName = $config->getTable();
        $this->connection = $manager->connection($config->getConnection());
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        if (!$this->hasMigrateTable()) {
            $builder = $this->connection->createQueryBuilder($this->tableName);
            $this->connection->query(
                $builder->create(function (CreateQuery $query) {
                    $query->string('version', 30);
                    $query->longText('source');
                })
            );
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function versions()
    {
        if (!$this->hasMigrateTable()) {
            return [];
        }
        $builder = $this->connection->createQueryBuilder($this->tableName);
        $versions = $this->connection->all($builder->select()->orderBy('version'));
        return $versions->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function version($id)
    {
        if (!$this->hasMigrateTable()) {
            return null;
        }
        $builder = $this->connection->createQueryBuilder($this->tableName);
        return $this->connection->first($builder->select()->where('version', $id));
    }

    /**
     * {@inheritdoc}
     */
    public function up($id, $source)
    {
        $this->connection->query(
            $this->connection->createQueryBuilder($this->tableName)->insert([
                'version' => $id,
                'source' => $source,
            ])
        );
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function down($id)
    {
        $this->connection->query(
            $this->connection->createQueryBuilder($this->tableName)->delete()->where([
                'version' => $id,
            ])
        );
        return $this;
    }

    /**
     * @return bool
     */
    protected function hasMigrateTable()
    {
        $builder = $this->connection->createQueryBuilder($this->tableName);
        try {
            $this->connection->first($builder->select()->take(1));
        } catch (\PDOException $e) {
            return false;
        }
        return true;
    }
}
