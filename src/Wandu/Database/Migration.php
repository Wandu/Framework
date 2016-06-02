<?php
namespace Wandu\Database;

use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Capsule\Manager as Capsule;

abstract class Migration
{
    const TEMPLATE_PATH = __DIR__ . '/phpmig.tmpl';

    public function up()
    {
        $this->migrate(Capsule::schema());
    }

    public function down()
    {
        $this->rollback(Capsule::schema());
    }

    /**
     * @param string $name
     * @return \Illuminate\Database\Connection
     */
    protected function connection($name)
    {
        return Capsule::connection($name);
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
