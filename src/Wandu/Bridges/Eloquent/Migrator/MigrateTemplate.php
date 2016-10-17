<?php
namespace Wandu\Bridges\Eloquent\Migrator;

use Wandu\Database\Migrator\MigrateTemplateInterface;

class MigrateTemplate implements MigrateTemplateInterface 
{
    /**
     * {@inheritdoc}
     */
    public function getContext($migrateName)
    {
        return <<<PHP
<?php

use Wandu\Bridges\Eloquent\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

class {$migrateName} extends Migration
{
    /**
     * @param \Illuminate\Database\Schema\Builder \$schema
     */
    public function migrate(Builder \$schema)
    {
        \$schema->create('somethings', function (Blueprint \$table) {
            \$table->bigIncrements('id');
            \$table->timestamps();
        });
    }

    /**
     * @param \Illuminate\Database\Schema\Builder \$schema
     */
    public function rollback(Builder \$schema)
    {
        \$schema->dropIfExists('somethings');
    }
}

PHP;

    }
}
