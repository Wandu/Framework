<?php
namespace Wandu\Bridges\Eloquent\Migrator;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Wandu\Database\Migrator\MigrateAdapterInterface;
use Wandu\Database\Migrator\Configuration;

class MigrateAdapter implements MigrateAdapterInterface
{
    /** @var \Illuminate\Database\Connection */
    protected $connection;

    /** @var string */
    protected $tableName;

    /**
     * @param \Illuminate\Database\Capsule\Manager $manager
     * @param \Wandu\Database\Migrator\Configuration $config
     */
    public function __construct(Manager $manager, Configuration $config)
    {
        $this->tableName = $config->getTable();
        $this->connection = $manager->getConnection($config->getConnection());
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        $schema = $this->connection->getSchemaBuilder();
        if ($schema->hasTable($this->tableName)) {
            return $this;
        }
        $schema->create($this->tableName, function (Blueprint $table) {
            $table->string('version', 30);
            $table->longText('source');
        });
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function versions()
    {
        $schema = $this->connection->getSchemaBuilder();
        if (!$schema->hasTable($this->tableName)) {
            return [];
        }
        $versions = $this->connection->table($this->tableName)->orderBy('version')->get();
        return array_map(function ($item) {
            return (array) $item;
        }, $versions->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function version($id)
    {
        $schema = $this->connection->getSchemaBuilder();
        if (!$schema->hasTable($this->tableName)) {
            return null;
        }
        $version = $this->connection->table($this->tableName)->where('version', $id)->first();
        if ($version) {
            return (array) $version;
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function up($id, $source)
    {
        $this->connection->table($this->tableName)->insert([
            'version' => $id,
            'source' => $source,
        ]);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function down($id)
    {
        $this->connection->table($this->tableName)->where('version', $id)->delete();
        return $this;
    }
}
