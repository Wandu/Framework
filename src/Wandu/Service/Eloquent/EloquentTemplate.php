<?php
namespace Wandu\Service\Eloquent;

use Wandu\Migrator\Contracts\MigrationTemplate;

class EloquentTemplate implements MigrationTemplate
{
    /**
     * {@inheritdoc}
     */
    public function template($migrateName)
    {
        return <<<PHP
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Wandu\Service\Eloquent\Migration;

class {$migrateName} extends Migration
{
    /**
     * @param \Illuminate\Database\Schema\Builder \$schema
     */
    public function migrate(Builder \$schema)
    {
    }

    /**
     * @param \Illuminate\Database\Schema\Builder \$schema
     */
    public function rollback(Builder \$schema)
    {
    }
}

PHP;
    }
}
