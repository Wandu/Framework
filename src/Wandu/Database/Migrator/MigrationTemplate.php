<?php
namespace Wandu\Database\Migrator;

use Wandu\Database\Contracts\Migrator\MigrationTemplateInterface;

class MigrationTemplate implements MigrationTemplateInterface
{
    /**
     * {@inheritdoc}
     */
    public function template($migrateName)
    {
        return <<<PHP
<?php

use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Migrator\Migration;
use Wandu\Database\Query\CreateQuery;
use Wandu\Database\Query\Expression\RawExpression;

class {$migrateName} extends Migration 
{
    /**
     * {@inheritdoc}
     */
    public function migrate(ConnectionInterface \$connection)
    {
        \$connection->query(\$connection->createQueryBuilder('somethings')->create(function (CreateQuery \$table) {
            \$table->bigInteger('id')->unsigned()->autoIncrement();

            \$table->timestamp('created_at')->default(new RawExpression('CURRENT_TIMESTAMP'));
            \$table->addColumn(new RawExpression('`updated_at` TIMESTAMP DEFAULT now() ON UPDATE now()'));

            \$table->primaryKey('id');
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function rollback(ConnectionInterface \$connection)
    {
        \$connection->query(\$connection->createQueryBuilder('somethings')->drop());
    }
}

PHP;
    }
}
